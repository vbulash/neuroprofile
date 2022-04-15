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
		$questions = $context['set']->questions();
		$count = $questions->count();

		$first = $last = null;
		if($count > 1) {
			$first = $questions->first()->getKey();
			$last = $questions->last()->getKey();
		}

		return Datatables::of($questions)
			->editColumn('preview', fn($question) => 'Не реализовано')
			->editColumn('learning', fn($question) => $question->learning ? 'Учебный' : 'Реальный')
			->editColumn('key', fn($question) => $question->value1 . '|' . $question->value2)
			->addColumn('action', function ($question) use($first, $last, $count) {
				$editRoute = route('questions.edit', ['question' => $question->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('questions.show', ['question' => $question->getKey(), 'sid' => session()->getId()]);
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
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$question->getKey()}, '{$question->sort_no}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				if($count > 1) {
					if ($question->getKey() != $first)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-info btn-sm float-left mr-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выше\" onclick=\"clickUp({$question->getKey()})\">\n" .
							"<i class=\"fas fa-arrow-up\"></i>\n" .
							"</a>\n";
					if ($question->getKey() != $last)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-info btn-sm float-left mr-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Ниже\" onclick=\"clickDown({$question->getKey()})\">\n" .
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

        $count = $context['set']->questions()->count();
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
		$data['sort_no'] = $set->questions()->count() + 1;

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

	private function move(int $id, bool $up)
	{
		$question = Question::findOrFail($id);

		$set = $question->set;
		$questions = $set->questions()
			->sortBy('sort_no')
			->pluck('sort_no', 'id')
			->toArray();

		$indexes = array_keys($questions);

		$currentPos = array_search($question->getKey(), $indexes);
		$currentID = $question->getKey();
		$currentOrder = $question->sort_no;
		$current = Question::findOrFail($currentID);

		$targetPos = ($up ? $currentPos - 1 : $currentPos + 1);
		$targetID = $indexes[$targetPos];
		$targetOrder = $questions[$targetID];
		$target = Question::findOrFail($targetID);

		// Обмен sort_no в 2 записях в рамках транзакции
		DB::transaction(function () use ($current, $target, $currentOrder, $targetOrder) {
			$current->update([
				'sort_no' => $targetOrder
			]);
			$target->update([
				'sort_no' => $currentOrder
			]);
		});
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
		$id = $request->id;
		$this->move($id, true);
		event(new ToastEvent('success', '', 'Вопрос перемещен ближе к началу списка'));

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
		$id = $request->id;
		$this->move($id, false);
		event(new ToastEvent('success', '', 'Вопрос перемещен ближе к концу списка'));

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
