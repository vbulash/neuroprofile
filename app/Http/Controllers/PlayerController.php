<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\results\BlocksArea;
use App\Http\Controllers\results\BlocksComposer;
use App\Http\Controllers\results\CardComposer;
use App\Http\Requests\PKeyRequest;
use App\Mail\TestClientResult;
use App\Mail\TestResult;
use App\Models\Contract;
use App\Models\History;
use App\Models\HistoryStep;
use App\Models\License;
use App\Models\Test;
use App\Models\TestOptions;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DateTime;

class PlayerController extends Controller {
	public function check(Request $request, string $mkey = null, string $test_key = null): bool {
		//        Log::debug('mkey = ' . $mkey);
//        Log::debug('test_key = ' . $test_key);
		if (!$mkey) {
			if (!session()->has('mkey')) {
				Log::debug('Внутренняя ошибка: потерян мастер-ключ');
				session()->flash('error', 'Внутренняя ошибка: потерян мастер-ключ');
				return false;
			} else
				return true;
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

	public function index(Request $request): Factory|View|Application {
		$test = $request->test ?: session('test');
		return view('front.index', compact('test'));
	}

	public function play(Request $request, string $mkey = null, string $test = null): Factory|View|RedirectResponse|Application {
		$mkey = $mkey ?: $request->{ 'mkey-modal'};
		$test = $test ?: $request->test;
		if (!$this->check($request, $mkey, $test)) {
			//Log::debug('player.play: ' . __METHOD__ . ':' . __LINE__);
			return redirect()->route('player.index');
		} else {
			$test = session('test');
			$content = json_decode($test->content, true);
			$branding = null;
			if (isset($content['branding'])) {
				if (!isset($content['branding']['background']))
					$content['branding']['background'] = '#007bff';
				if (!isset($content['branding']['fontcolor']))
					$content['branding']['fontcolor'] = '#ffffff';
				$branding = [
					'company' => isset($content['branding']['company-name']) ? $content['branding']['company-name'] : '',
					'logo' => isset($content['branding']['logo']) ? $content['branding']['logo'] : null,
					'navstyle' => sprintf("background-color: %s!important; color: %s !important",
						$content['branding']['background'], $content['branding']['fontcolor']),
					'textstyle' => sprintf("color: %s !important", $content['branding']['fontcolor']),
					'buttonstyle' => sprintf("background-color: %s!important; color: %s !important; border-color: %s !important",
						$content['branding']['background'], $content['branding']['fontcolor'], $content['branding']['background'])
				];
			}
			session()->put('branding', $branding);
			return view('front.intro', compact('test'));
		}
	}

	public function card(Request $request): View|Factory|bool|RedirectResponse|Application {
		if (!$this->check($request)) {
			//Log::debug('player.card: ' . __METHOD__ . ':' . __LINE__);
			return redirect()->route('player.index');
		} else {
			session()->forget('pkey');
			$test = session('test');

			if ($test->options & TestOptions::AUTH_GUEST->value) {
				//return redirect()->route('player.body', ['question' => 0]);
				return redirect()->route('player.body2');
			} elseif ($test->options & (TestOptions::AUTH_FULL->value | TestOptions::AUTH_MIX->value)) {
				$content = json_decode($test->content, true);
				$card = $content['card'];
				$mix = $test->options & TestOptions::AUTH_MIX->value;
				return view('front.full_card', compact('test', 'card', 'mix'));
			} elseif ($test->options & TestOptions::AUTH_PKEY->value) {
				return view('front.pkey_card', compact('test'));
			}
		}
		return false;
	}

	public function store_pkey(PKeyRequest $request) {
		session()->forget('pkey');
		session()->put('pkey', $request->pkey);
		return redirect()->route('player.body2', ['question' => 0]);
	}

	public function store_full_card(Request $request) {
		$data = $request->except(['privacy_policy', 'privacy_personal', '_token']);

		session()->forget('pkey');
		if ($request->has('pkey'))
			session()->put('pkey', $request->pkey);
		;
		session()->put('card', $data);

		//return redirect()->route('player.body', ['question' => 0]);
		return redirect()->route('player.body2');
	}

	public function body2(Request $request) {
		if (!$this->check($request)) {
			$test = session('test');
			//Log::debug('player.body2: ' . __METHOD__ . ':' . __LINE__);
			return redirect()->route('player.index');
		}

		$test = session('test');
		$questions = $test->set->questions->sortBy('sort_no');

		// Блокировка лицензии для прохождения теста
		$license = null;
		if (session()->has('pkey')) { // Режим авторизации по персональному ключу или микс
			// Найти и проверить лицензию по введенному персональному ключу
			$license = License::where('pkey', session('pkey'))->first();
			if (!$license) {
				session()->put('error', 'Не найдена лицензия, соответствующая персональному ключу ' . session('pkey'));
				return redirect()->route('player.index');
			} elseif ($license->status != License::FREE) {
				session()->put('error', 'Лицензия, соответствующая персональному ключу ' . session('pkey') . ', уже использована. Запросите новый персональный ключ');
				return redirect()->route('player.index');
			}
		} else { // Найти любую свободную лицензию
			$license = $test->contract->licenses->where('status', License::FREE)->first();
			if ($license) {
				session()->put('pkey', $license->pkey);
			} else {
				session()->put('error', 'Свободные лицензии закончились, обратитесь в Persona');
				//Log::debug(__METHOD__ . ':' . __LINE__);
				return redirect()->route('player.index');
			}
		}
		$license->lock();

		return view('front.body2', compact('test', 'questions'));
	}

	public function body2_store(Request $request): RedirectResponse {
		event(new ToastEvent('info', '', "Анализ результатов тестирования..."));

		$test = session('test');
		$data = $request->except(['privacy_policy', 'privacy_personal', '_token']);

		// Фиксация лицензии по завершению тестирования
		$license = License::all()->where('pkey', session('pkey'))->first();
		$license->done();

		// Зафиксировать историю теста и индивидуальные результаты прохождения вопросов

		$history = new History();
		$history->card = session()->has('card') ? json_encode(session('card')) : null;
		$history->paid = false;
		$history->test()->associate($test);
		$history->license()->associate($license);
		$history->save();

		foreach ($data as $answer => $value) {
			if (!Str::startsWith($answer, 'answer-')) continue;
			$parts = explode('-', $answer);
			$key = $parts[1];

			$step = new HistoryStep();
			$step->key = $value;
			$step->history()->associate($history);
			$step->question()->associate($key);
			$step->done = new DateTime();
			$step->save();
		}
		$history->update(['done' => new DateTime()]);

		return redirect()->route('player.calculate', ['history_id' => $history->getKey()]);
	}

	public function calculate(
		int $history_id, bool $repeat = false, bool $historyMode = false, bool $pay = false
	) {
		$history = History::findOrFail($history_id);
		if (env('RESEARCH')) {
			$test = $history->test;
			return view('front.thanks', compact('test'));
		}

		if ($pay)
			$history->update(['paid' => true]);

		if (!$repeat) {
			// Не переименовывать переменную - может использоваться в коде набора вопросов в eval()
			$result = $history->steps()->pluck('key')->toArray();

			$code = htmlspecialchars_decode(strip_tags($history->test->set->code));
			$history->code = eval($code);
			$history->update();
			// Код нейропрофиля вычислен и сохранен
		}

		$content = json_decode($history->test->content);
		$maildata = [];
		$maildata['show'] = $content->descriptions->show ?? false;
		$maildata['mail'] = $content->descriptions->mail ?? false;
		$maildata['client'] = $content->descriptions->client ?? false;
		$maildata['$branding'] = $content->branding ?? false;

		if ($maildata['mail'])
			$this->mailRespondent($history, $maildata, $historyMode);
		if (!$pay && $maildata['client'])
			$this->mailClient($history, $maildata, $historyMode);

		$card = (new CardComposer($history))->getCard();
		$composer = new BlocksComposer($history);

		if (!$repeat) {
			if ($maildata['show']) {
				$profile = $composer->getProfile(BlocksArea::SHOW);
				$blocks = $composer->getBlocks($profile);

				event(new ToastEvent('success', '', 'Результаты тестирования отображаются на экране'));
				if ($blocks)
					return view('front.show', compact('card', 'blocks', 'profile', 'history'));
			} else
				return redirect()->route('player.index');
		}

		return response(content: 'OK' . $history_id, status: 200);
	}

	public function iframe(): Factory|View|Application {
		return view('front.iframe');
	}

	public function showDocument(Request $request, string $document, bool $mail = false): Factory|View|Application {
		$test = session('test');
		$docviews = [
			'privacy' => 'front.documents.privacy',
			'personal' => 'front.documents.personal',
			'oferta' => 'front.documents.oferta',
		];

		return view($docviews[$document], compact('mail', 'test'));
	}

	/**
	 * @param History $history
	 * @param array $maildata
	 * @param bool $historyMode
	 * @return void
	 */
	private function mailRespondent(History $history, array $maildata, bool $historyMode): void {
		$card = (new CardComposer($history))->getCard();
		$composer = new BlocksComposer($history);
		$profile = $composer->getProfile(BlocksArea::MAIL);
		$blocks = $composer->getBlocks($profile);

		$recipient = (object) [
			'name' =>
			join(' ', [$card['Фамилия'] ?? null, $card['Имя'] ?? null, $card['Отчество'] ?? null]),
			'email' => $card['Электронная почта']
		];
		$copy = (object) [
			'name' => env('MAIL_FROM_NAME'),
			'email' => env('MAIL_FROM_ADDRESS')
		];

		$testResult = new TestResult($history, $blocks, $card, $profile, $maildata['branding'] ?? null);
		try {
			if ($historyMode)
				Mail::to($recipient)
					->send($testResult);
			else
				Mail::to($recipient)
					->cc($copy)
					->send($testResult);

			event(new ToastEvent('success', '', 'Вам отправлено письмо с результатами тестирования'));
			//			session()->put('success', 'Вам отправлено письмо с результатами тестирования');
		} catch (Exception $exc) {
			session()->put('error', "Ошибка отправки письма с результатами тестирования:<br/>" .
				$exc->getMessage());
		}
	}

	/**
	 * @param History $history
	 * @param array $maildata
	 * @param bool $historyMode
	 * @return void
	 */
	private function mailClient(History $history, array $maildata, bool $historyMode): void {
		if ($historyMode)
			return;

		$card = (new CardComposer($history))->getCard();
		$composer = new BlocksComposer($history);
		$profile = $composer->getProfile(BlocksArea::CLIENT);
		$blocks = $composer->getBlocks($profile);

		$recipient = (object) [
			'name' => $history->test->contract->client->name,
			'email' => $history->test->contract->client->email
		];

		try {
			Mail::to($recipient)
				->send(new TestClientResult($history, $blocks, $card, $profile, $maildata['branding'] ?? null));

			//event(new ToastEvent('success', '', 'Вам отправлено письмо с результатами тестирования'));
		} catch (Exception $exc) {
			session()->put('error', "Ошибка отправки письма клиенту с результатами тестирования:<br/>" .
				$exc->getMessage());
		}
	}

	/**
	 * Результат оплаты в Робокасса
	 */
	public function paymentResult(Request $request) {
		//		Log::debug('Robokassa result = ' . print_r($request->all(), true));
		$history_id = $request->InvId;
		$session = $request->Shp_Session ?? null;
		if ($session && $session != session()->getId()) {
			session()->setId($session);
			session()->start();
		}

		return $this->calculate($history_id, true, true, true);
	}

	public function paymentSuccess(Request $request) {
		//		Log::debug('Robokassa success = ' . print_r($request->all(), true));
		$history_id = $request->InvId;
		$session = $request->Shp_Session ?? null;
		$history = History::findOrFail($history_id);
		$test = $history->test;
		if ($session && $session != session()->getId()) {
			session()->setId($session);
			session()->start();
		}

		session()->put('success', 'Вам отправлено письмо с полными результатами тестирования');

		if (auth()->check())
			return redirect()->route('dashboard');
		else
			return response(status: 200);
	}

	public function paymentFail(Request $request) {
		//		Log::debug('Robokassa fail = ' . print_r($request->all(), true));
		$history_id = $request->InvId;
		$mail = ($request->Shp_Mail == '1');
		$session = $request->Shp_Session ?? null;
		if ($session && $session != session()->getId()) {
			session()->setId($session);
			session()->start();
		}

		$history = History::findOrFail($history_id);
		$test = $history->test;

		session()->forget('error');

		if (auth()->check())
			return redirect()->route('dashboard');
		else
			return response(status: 200);
	}

	/**
	 * Повтор электронных писем по итогам тестирования через историю (history.index)
	 * @param Request $request
	 */
	public function mail(Request $request) {
		return $this->calculate($request->history, true, true);
	}
}
