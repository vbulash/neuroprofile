<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\PKeyRequest;
use App\Models\Contract;
use App\Models\FMPType;
use App\Models\History;
use App\Models\HistoryStep;
use App\Models\License;
use App\Models\Profile;
use App\Models\Test;
use App\Models\TestOptions;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PlayerController extends Controller
{
    public function check(Request $request, string $mkey = null, string $test_key = null): bool
    {
//        Log::debug('mkey = ' . $mkey);
//        Log::debug('test_key = ' . $test_key);
        if (!$mkey) {
            if (!session()->has('mkey')) {
                Log::debug('Внутренняя ошибка: потерян мастер-ключ');
                session()->flash('error', 'Внутренняя ошибка: потерян мастер-ключ');
                return false;
            } else return true;
        }
        session()->forget('test');

        $contract = Contract::all()->where('mkey', $mkey)->first();
        if (!$contract) {
            Log::debug('Неверный мастер-ключ');
            session()->flash('error', 'Неверный мастер-ключ');
            return false;
        } else {
            $messages = [];

            // Проверка URL вызова
            $contractUrl = parse_url($contract->url);
            $realUrl = parse_url($request->server('HTTP_REFERER'));
            //Log::debug('server = ' . print_r($request->server(), true));
            // TODO Включить проверку URL
            //$result = Str::startsWith($realUrl['scheme'] . '://' . $realUrl['host'], $contractUrl['scheme'] . '://' . $contractUrl['host']);
            $result = true;
//            Log::debug('contractUrl = ' . $contractUrl['scheme'] . '://' . $contractUrl['host'] .
//                ' | realUrl = ' . $realUrl['scheme'] . '://' . $realUrl['host'] .
//                ' | compare = ' . $result);
            if (!$result) {
                $messages[] = 'Запуск теста с текущей страницы не разрешен';
            } else {
                $test = Test::all()->where('key', $test_key)->first();
                if (!$test) {
                    //Log::debug(__METHOD__ . ':' . __LINE__);
                    $messages[] = 'Не найден тест с указанным ключом';
                } else {
                }
            }

            if (count($messages) > 0) {
                session()->flash('error', implode('<br/>', $messages));
                Log::debug('Сообщения об ошибках: <br/>' . implode('<br/>', $messages));
                return false;
            } else {
                session()->put('test', $test);
                session()->put('mkey', $mkey);
                //Log::debug('test and mkey saved');
                return true;
            }
        }
    }

    public function index(Request $request)
    {
		$test = $request->test ?: session('test');
        return view('front.index',compact('test'));
    }

    public function play(Request $request, string $mkey = null, string $test = null)
    {
        $mkey = $mkey ?: $request->{'mkey-modal'};
        $test = $test ?: $request->test;
        if (!$this->check($request, $mkey, $test)) {
            //Log::debug('player.play: ' . __METHOD__ . ':' . __LINE__);
            return redirect()->route('player.index', ['sid' => session()->getId()])->with('error', session('error'));
        } else {
            $test = session('test');
			$content = json_decode($test->content, true);
			$branding = null;
			if(isset($content['branding'])) {
				$branding = [
					'company' => isset($content['branding']['company-name']) ? $content['branding']['company-name'] : '',
					'logo' => isset($content['branding']['logo']) ? $content['branding']['logo'] : null,
					'navstyle' => sprintf("background-color: %s!important; color: %s !important",
						$content['branding']['background'],  $content['branding']['fontcolor']),
					'textstyle' => sprintf("color: %s !important", $content['branding']['fontcolor']),
					'buttonstyle' => sprintf("background-color: %s!important; color: %s !important; border-color: %s !important",
						$content['branding']['background'],  $content['branding']['fontcolor'], $content['branding']['background'])
				];
			}
			session()->put('branding', $branding);
			return view('front.intro', compact('test'));
        }
    }

    public function card(Request $request)
    {
        if (!$this->check($request)) {
            //Log::debug('player.card: ' . __METHOD__ . ':' . __LINE__);
            return redirect()->route('player.index', ['sid' => session()->getId()])->with('error', session('error'));
        } else {
            session()->forget('pkey');
            $test = session('test');

            if ($test->options & TestOptions::AUTH_GUEST->value) {
                //return redirect()->route('player.body', ['question' => 0, 'sid' => session()->getId()]);
                return redirect()->route('player.body2', ['sid' => session()->getId()]);
            } elseif ($test->options & (TestOptions::AUTH_FULL->value | TestOptions::AUTH_MIX->value)) {
                $content = json_decode($test->content, true);
                $card = $content['card'];
				$mix = $test->options & TestOptions::AUTH_MIX->value;
                return view('front.full_card', compact('test', 'card', 'mix'));
            } elseif ($test->options & TestOptions::AUTH_PKEY->value) {
                return view('front.pkey_card', compact('test'));
            }
        }
    }

    public function store_pkey(PKeyRequest $request)
    {
		session()->forget('pkey');
        session()->put('pkey', $request->pkey);
        return redirect()->route('player.body2', ['question' => 0, 'sid' => session()->getId()]);
    }

    public function store_full_card(Request $request)
    {
        $data = $request->except('_token');

		session()->forget('pkey');
		if($request->has('pkey'))
			session()->put('pkey', $request->pkey);;
        session()->put('card', $data);

        //return redirect()->route('player.body', ['question' => 0, 'sid' => session()->getId()]);
        return redirect()->route('player.body2', ['sid' => session()->getId()]);
    }

    public function body2(Request $request)
    {
        if (!$this->check($request)) {
			$test = session('test');
            //Log::debug('player.body2: ' . __METHOD__ . ':' . __LINE__);
            return redirect()->route('player.index', ['sid' => session()->getId()])->with('error', session('error'));
        }

        $test = session('test');
        $set = $test->qset;

        $view = DB::select(<<<EOS
SELECT
       t.id as tid,
       q.id as qid,
       q.sort_no as qsort_no,
       q.learning as qlearning,
       q.timeout as qtimeout,
       q.image1 as qimage1,
       q.image2 as qimage2,
       q.value1 as qvalue1,
       q.value2 as qvalue2
FROM
    tests AS t, sets AS s, questions as q
WHERE
    s.id = t.set_id AND
    q.set_id = s.id AND
    t.id = :tid
ORDER BY
    qsort_no
EOS
            , ['tid' => $test->getKey()]
        );

        $stack = [];
        $steps = [];
        foreach ($view as $item) {
            $stack[] = $item->qid;
            $step = [
                'id' => $item->qid,
                'sort_no' => $item->qsort_no,
                'learning' => $item->qlearning,
                'timeout' => env('QUESTION_TIMEOUT') ?  $item->qtimeout : '0',
                'quantity' => 2
            ];

            $images = [];
            for ($iimage = 1; $iimage <= 2; $iimage++)
                $images[$item->{'qimage' . $iimage}] = $item->{'qvalue' . $iimage};
            $step['images'] = $images;

            $steps[] = $step;
        }

        // Блокировка лицензии для прохождения теста
		$license = null;
		if(session()->has('pkey')) { // Режим авторизации по персональному ключу или микс
			// Найти и проверить лицензию по введенному персональному ключу
			$license = License::where('pkey', session('pkey'))->first();
			if(!$license) {
				session()->put('error', 'Не найдена лицензия, соответствующая персональному ключу ' . session('pkey'));
				return redirect()->route('player.index', ['sid' => session()->getId()]);
			} elseif ($license->status != License::FREE) {
				session()->put('error', 'Лицензия, соответствующая персональному ключу ' . session('pkey') . ', уже использована. Запросите новый персональный ключ');
				return redirect()->route('player.index', ['sid' => session()->getId()]);
			}
		} else {	// Найти любую свободную лицензию
			$license = $test->contract->licenses->where('status', License::FREE)->first();
			if ($license) {
				session()->put('pkey', $license->pkey);
			} else {
				session()->put('error', 'Свободные лицензии закончились, обратитесь в Persona');
				//Log::debug(__METHOD__ . ':' . __LINE__);
				return redirect()->route('player.index', ['sid' => session()->getId()]);
			}
		}
        $license->lock();

        return view('front.body2', compact('test', 'steps', 'stack'));
    }

    public function body2_store(Request $request): RedirectResponse
    {
        event(new ToastEvent('info', '', "Анализ результатов тестирования..."));

        $test = session('test');
        $data = $request->all();

        // Фиксация лицензии по завершению тестирования
        $license = License::all()->where('pkey', session('pkey'))->first();
        $license->done();

        // Зафиксировать историю теста и индивидуальные результаты прохождения вопросов в рамках транзакции
        DB::transaction(function () use ($data, $license, $test) {
            $history = History::create([
                'test_id' => $test->getKey(),
                'license_id' => $license->getKey(),
                'card' => (session()->has('card') ? json_encode(session('card')) : null),
            ]);
            $history->save();
            session()->put('hid', $history->getKey());

            foreach ($data as $answer => $value) {
                if (!Str::startsWith($answer, 'answer-')) continue;
                $parts = explode('-', $answer);
                $key = $parts[1];
                // $key => $value
                $hs = HistoryStep::create([
                    'history_id' => $history->getKey(),
                    'question_id' => $key,
                    'key' => $value,
                    'done' => date("Y-m-d H:i:s")
                ]);
                $hs->save();
            }

            $history->update(['done' => date("Y-m-d H:i:s")]);
        });

        $hid = session('hid');
        session()->forget('hid');
        return redirect()->route('player.precalc',
            [
                'test' => $test->getKey(),
                'history_id' => $hid,
                'sid' => session()->getId()
            ]
        );
    }

    public function precalc(int $history_id)
    {
        $history = History::findOrFail($history_id);
        $test = $history->test;
        return view('front.precalc', compact('test', 'history_id'));
    }

    public function calculate(int $history_id): View|Factory|bool|Application|RedirectResponse|null
    {
        $history = History::findOrFail($history_id);
        $test = $history->test;
        $content = json_decode($test->content);

        // Не переименовывать переменную - может использоваться в коде набора вопросов в eval()
        $result = HistoryStep::where('history_id', $history_id)->pluck('key')->toArray();

        $code = htmlspecialchars_decode(strip_tags($history->test->set->code));
        $profile_code = eval($code);

		if($profile_code == null) // Ситуация фантастическая, но встречается очень часто
			$history->delete();
		else {
			$history->code = $profile_code;
			$history->update();
			// Код нейропрофиля вычислен и сохранен

			session()->put('success', "Результат тестирования = $profile_code");
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}

        return null;
    }

	private function getProfile(int $fmptype_id, string $code): ?Profile
	{
		$fmtype = FMPType::find($fmptype_id);
		$profile = Profile::all()
			->where('fmptype_id', $fmptype_id)
			->where('code', $code)
			->first();
		if($profile) {
			return $profile;
		} else {
			session()->put('error', "Не найден нейропрофиль \"{$code}\" из типа описания ФМП \"{$fmtype->name}\". Добавьте нейропрофиль");
			return null;
		}
	}

    public function iframe()
    {
        return view('front.iframe');
    }

    public function showDocument(Request $request, string $document, bool $mail = false)
    {
        $test = session('test');
        $docviews = [
            'privacy' => 'front.documents.privacy',
            'personal' => 'front.documents.personal',
            'oferta' => 'front.documents.oferta',
        ];

        return view($docviews[$document], compact('mail', 'test'));
    }
}
