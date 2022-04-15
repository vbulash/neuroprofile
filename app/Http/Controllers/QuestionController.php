<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Contract;
use App\Models\License;
use App\Models\Question;
use App\Models\Set;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

class QuestionController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$context = session('context');
		$set = $context['set'];
		$questions = DB::select(<<< SQL
SELECT questions.* FROM questions, sets
WHERE sets.id = questions.set_id AND sets.id = :id
ORDER BY sort_no
SQL,
			['id' => $set->getKey()]);

		$count = count($questions);
		$first = $last = 0;
		if($count > 1) {
			$first = $questions[0]->id;
			$last = $questions[$count - 1]->id;
		}

		return Datatables::of($questions)
			->editColumn('preview', fn($question) => 'Не реализовано')
			->editColumn('learning', fn($question) => $question->learning ? 'Учебный' : 'Реальный')
			->editColumn('key', fn($question) => $question->value1 . '|' . $question->value2)
			->addColumn('action', function ($question) use($first, $last, $count) {
				$editRoute = route('questions.edit', ['question' => $question->id, 'sid' => session()->getId()]);
				$showRoute = route('questions.show', ['question' => $question->id, 'sid' => session()->getId()]);
				$actions = '';

				$actions .=
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-pencil-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$question->id}, '{$question->sort_no}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				if($count > 1) {
					if ($question->id != $first)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выше\" onclick=\"clickUp({$question->id})\">\n" .
							"<i class=\"fas fa-arrow-up\"></i>\n" .
							"</a>\n";
					if ($question->id != $last)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Ниже\" onclick=\"clickDown({$question->id})\">\n" .
							"<i class=\"fas fa-arrow-down\"></i>\n" .
							"</a>\n";
				}

				return $actions;
			})
			->make(true);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
	 */
    public function index()
    {
		$context = session('context');
		unset($context['question']);
		session()->put('context', $context);

        $count = $context['set']->questions->count();
		return view('questions.index', compact('count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
	 */
    public function create()
    {
		$context = session('context');
		$set = $context['set'];
		return view('questions.create', compact('set'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreQuestionRequest $request
	 * @return RedirectResponse
	 */
    public function store(StoreQuestionRequest $request)
    {
		$context = session('context');
		$set = $context['set'];

		$data = $request->all();
		$data['sort_no'] = $set->questions->count() + 1;

		$question = Question::create($data);
		$question->save();

		session()->put('success', "Вопрос № {$data['sort_no']} из набора вопросов '{$set->name}' создан");
		return redirect()->route('questions.index', ['sid' => session()->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

	private function reorder(array $ids): void
	{
		DB::transaction(function () use ($ids) {
			$counter = 0;
			foreach ($ids as $id) {
				$counter++;
				$question = Question::findOrFail($id);
				if($question->sort_no != $counter)
					$question->update(['sort_no' => $counter]);
			}
		});
	}

	private function move(int $id, bool $up)
	{
		$question = Question::findOrFail($id);
		$questions = $question->set->questions
			->sortBy('sort_no')
			->pluck('id')
			->toArray();

		$currentPos = array_search($question->getKey(), $questions);
		$targetPos = ($up ? $currentPos - 1 : $currentPos + 1);
		$buffer = $questions[$targetPos];
		$questions[$targetPos] = $questions[$currentPos];
		$questions[$currentPos] = $buffer;

		$this->reorder($questions);
	}

	/**
	 * Move question up on sort order
	 *
	 * @param Request $request
	 * @param int $id
	 * @return bool
	 */
	public function up(Request $request)
	{
		$this->move($request->id, true);
		//event(new ToastEvent('success', '', 'Вопрос перемещен ближе к началу списка'));

		return true;
	}

	/**
	 * Move question down on sort order
	 *
	 * @param Request $request
	 * @param int $id
	 * @return bool
	 */
	public function down(Request $request)
	{
		$this->move($request->id, false);
		//event(new ToastEvent('success', '', 'Вопрос перемещен ближе к концу списка'));

		return true;
	}
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
