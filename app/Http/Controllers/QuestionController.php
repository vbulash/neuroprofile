<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\FileLink;
use App\Models\Kind;
use App\Models\Part;
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
					if ($path)
						$data[] = '/uploads/' . $path;
				}
				return ($data ? json_encode($data) : '');
			})
			->editColumn('learning', fn($question) => $question->learning ? 'Учебный' : 'Реальный')
			->editColumn('cue', fn($question) => $question->cue ? 'Есть' : 'Нет')
			->editColumn('kind', fn($question) => $question->kind->name)
			->addColumn('action', function ($question) use ($first, $last, $count) {
				$editRoute = route('questions.edit', ['question' => $question->getKey()]);
				$showRoute = route('questions.show', ['question' => $question->getKey()]);
				$selectRoute = route('questions.select', ['question' => $question->getKey()]);

				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-pencil-alt', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete({$question->getKey()}, '{$question->sort_no}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'click' => "clickDuplicate({$question->getKey()})", 'icon' => 'fas fa-clone', 'title' => 'Дублирование'];
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Изображения для вопросов'];

				$actions = createDropdown('Действия', $items);
				$actions .= '<div class="me-4"></div>';

				if ($count > 1) {
					if ($question->getKey() != $first)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выше\" onclick=\"clickUp({$question->getKey()})\">\n" .
							"<i class=\"fas fa-arrow-up\"></i>\n" .
							"</a>\n";
					if ($question->getKey() != $last)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Ниже\" onclick=\"clickDown({$question->getKey()})\">\n" .
							"<i class=\"fas fa-arrow-down\"></i>\n" .
							"</a>\n";
				}

				return $actions;
			})
			->make(true);
	}

	public function select(int $id) {
		$context = session('context');
		$context['question'] = $id;
		session()->put('context', $context);

		return redirect()->route('parts.index');
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
		$mode = config('global.create');
		$kind = Kind::findOrFail($request->kind);
		return view('questions.create', compact('mode', 'kind'));
	}

	public function store(StoreQuestionRequest $request) {
		$context = session('context');
		$set = Set::findOrFail($context['set']);

		$question = new Question();
		$sort_no = $question->sort_no = $set->questions->count() + 1;
		$question->learning = $request->learning;
		$question->timeout = $request->timeout;
		$question->cue = $request->has('cue') ? $request->cue : '';
		$question->set()->associate($set->getKey());
		$question->kind()->associate($request->kind);
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

		$number = $question->sort_no;
		$question->update(
			[
				'learning' => $request->learning,
				'timeout' => $request->timeout,
				'cue' => $request->has('cue') ? $request->cue : ''
			]
		);

		// Перенумеровать по порядку после обновления
		$questions = $question->set->questions
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($questions);

		session()->put('success', "Вопрос ID {$number} из набора вопросов &laquo;{$set->name}&raquo; обновлён.<br/>Список вопросов перенумерован");
		return redirect()->route('questions.index');
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
		$number = $question->sort_no;
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

	/**
	 * Дублирование вопроса
	 *
	 * @param Request $request
	 * @return bool
	 */
	public function duplicate(Request $request) {
		$context = session('context');
		$set = Set::findOrFail($context['set']);
		$source = Question::findOrFail($request->id);

		$sort_no = $source->sort_no;
		$name = $set->name;

		$target = new Question();
		$target->sort_no = $set->questions->count() + 1;
		$target->learning = $source->learning;
		$target->timeout = $source->timeout;
		$target->cue = $source->cue;
		$target->set()->associate($set->getKey());
		$target->kind()->associate($source->kind);
		$target->save();

		foreach ($source->parts as $source_part) {
			$part = new Part();
			$part->image = $source_part->image;
			FileLink::link($part->image);
			$part->key = $source_part->key;
			$part->question()->associate($target);
			$part->save();
		}

		// Перенумеровать по порядку после создания
		$questions = $target->set->questions
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($questions);

		event(new ToastEvent('success', '', "Создан дубль вопроса № {$sort_no} из набора вопросов &laquo;{$name}&raquo;.<br/>Список вопросов перенумерован"));
		return true;
	}
}