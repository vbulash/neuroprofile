<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\FileLink;
use App\Models\Kind;
use App\Models\Question;
use App\Models\QuestionKind;
use App\Models\Set;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

class QuestionController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$context = session('context');
		$set = Set::findOrFail($context['set']);
		$questions = Question::where('set_id', $set->getKey())
			->get()
			->sortBy('sort_no');

		$count = $questions->count();
		$first = $last = 0;
		if ($questions->count() > 1) {
			$first = $questions->first()->getKey();
			$last = $questions->last()->getKey();
		}

		return Datatables::of($questions)
			->editColumn('preview', function ($question) {
			    $data = [];
			    foreach ($question->parts as $part) {
				    $path = $part->image;
				    if ($path) $data[] = '/uploads/' . $path;
			    }
			    return ($data ? json_encode($data) : '');
		    })
			->editColumn('learning', fn($question) => $question->learning ? 'Учебный' : 'Реальный')
			->editColumn('cue', fn($question) => $question->cue ? 'Есть' : 'Нет')
			->editColumn('kind', fn ($question) => $question->kind->name)
			->addColumn('action', function ($question) use ($first, $last, $count) {
			    $editRoute = route('questions.edit', ['question' => $question->getKey()]);
			    $showRoute = route('questions.show', ['question' => $question->getKey()]);
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

			    if ($count > 1) {
				    if ($question->getKey() != $first)
					    $actions .=
					    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выше\" onclick=\"clickUp({$question->getKey()})\">\n" .
					    	"<i class=\"fas fa-arrow-up\"></i>\n" .
					    	"</a>\n";
				    if ($question->getKey() != $last)
					    $actions .=
					    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Ниже\" onclick=\"clickDown({$question->getKey()})\">\n" .
					    	"<i class=\"fas fa-arrow-down\"></i>\n" .
					    	"</a>\n";
			    }

			    return $actions;
		    })
			->make(true);
	}

	public function index() {
		$context = session('context');
		unset($context['question']);
		session()->put('context', $context);

		$set = Set::findOrFail($context['set']);
		$count = $set->questions->count();
		$kinds = Kind::all();
		return view('questions.index', compact('count', 'kinds'));
	}

	public function create(Request $request) {
		$kind = Kind::findOrFail($request->kind);
		return view('questions.create', compact('kind'));
	}

	public function store(StoreQuestionRequest $request) {
		$context = session('context');
		$set = Set::findOrFail($context['set']);

		// switch ($request->kind) {
		// 	case QuestionKind::SINGLE2->value:
		// 		$data = $request->all();
		// 		$data['sort_no'] = $set->questions->count() + 1;

		// 		foreach (['image1', 'image2'] as $field) {
		// 			$mediaPath = Question::uploadImage($request, $field);
		// 			if ($mediaPath)
		// 				FileLink::link($mediaPath);
		// 			$data[$field] = $mediaPath;
		// 		}
		// 		break;
		// }

		$question = new Question();
		$sort_no = $question->sort_no = $set->questions->count() + 1;
		$question->learning = $request->learning;
		$question->timeout = $request->timeout;
		$question->cue = $request->has('cue') ? $request->cue : '';
		$question->kind->associate($request->kind);
		$question->save();

		// Перенумеровать по порядку после создания
		$questions = $question->set->questions
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($questions);

		session()->put('success', "Вопрос № {$sort_no} из набора вопросов &laquo;{$set->name}&raquo; создан.<br/>Список вопросов перенумерован");
		return redirect()->route('questions.index');
	}

	public function show($id) {
		return $this->edit($id, true);
	}

	public function edit(int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$question = Question::findOrFail($id);
		return view('questions.edit', compact('question', 'mode'));
	}

	public function update(UpdateQuestionRequest $request, int $id) {
		$context = session('context');
		$set = Set::findOrFail($context['set']);
		$question = Question::findOrFail($id);

		// switch ($request->kind) {
		// 	case QuestionKind::SINGLE2->value:
		// 		$data = $request->all();

		// 		foreach (['image1', 'image2'] as $field) {
		// 			if (!$request->has($field))
		// 				continue;

		// 			$mediaPath = Question::uploadImage($request, $field, $question->getAttribute($field));
		// 			if ($mediaPath)
		// 				FileLink::link($mediaPath);
		// 			$data[$field] = $mediaPath;
		// 		}
		// 		break;
		// }

		$number = $question->sort_no;
		$question->update([
			'learning' => $request->learning,
			'timeout' => $request->timeout,
			'cue' => $request->has('cue') ? $request->cue : ''
		]);

		// Перенумеровать по порядку после обновления
		$questions = $question->set->questions
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($questions);

		session()->put('success', "Вопрос ID {$number} из набора вопросов &laquo;{$set->name}&raquo; обновлён.<br/>Список вопросов перенумерован");
		return redirect()->route('questions.index', ['sid' => session()->getId()]);
	}

	private function reorder(array $ids): void {
		DB::transaction(function () use ($ids) {
			$counter = 0;
			foreach ($ids as $id) {
				$counter++;
				$question = Question::findOrFail($id);
				if ($question->sort_no != $counter)
					$question->update(['sort_no' => $counter]);
			}
		});
	}

	private function move(int $id, bool $up) {
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
	public function up(Request $request) {
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
	public function down(Request $request) {
		$this->move($request->id, false);
		//event(new ToastEvent('success', '', 'Вопрос перемещен ближе к концу списка'));

		return true;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $question
	 * @return bool
	 */
	public function destroy(Request $request, int $question) {
		if ($question == 0) {
			$id = $request->id;
		} else
			$id = $question;

		$question = Question::findOrFail($id);
		$number = $question->number;
		$name = $question->set->name;
		$question->delete();

		// Перенумеровать по порядку после удаления
		$questions = $question->set->questions
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($questions);

		event(new ToastEvent('success', '', "Вопрос № {$number} из набора вопросов &laquo;{$name}&raquo; удалён.<br/>Список вопросов перенумерован"));
		return true;
	}
}
