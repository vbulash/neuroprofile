<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdatePartRequest;
use App\Models\FileLink;
use App\Models\Part;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PartController extends Controller {
	public function getData(): JsonResponse {
		$context = session('context');
		$question = Question::findOrFail($context['question']);
		$parts = $question->parts;

		return DataTables::of($parts)
			->editColumn('preview', fn($part) => '/uploads/' . $part->image)
			->addColumn('action', function ($part) {
				$editRoute = route('parts.edit', ['part' => $part->getKey()]);
				$showRoute = route('parts.show', ['part' => $part->getKey()]);

				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-pencil-alt', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete({$part->getKey()})", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(Request $request) {
		$context = session('context');
		$question = Question::findOrFail($context['question']);
		$count = $question->parts()->count();

		return view('parts.index', compact('question', 'count'));
	}

	public function create(Request $request) {
		$mode = config('global.create');
		$context = session('context');
		$question = Question::findOrFail($context['question']);
		$keys = [];
		$temp = collect(explode(PHP_EOL, $question->kind->keys))
			->each(function ($key) use (&$keys) {
				$key = trim($key);
				$keys[$key] = $key;
			});

		return view('parts.create', compact('mode', 'question', 'keys'));
	}

	public function store(StorePartRequest $request) {
		$context = session('context');
		$question = Question::findOrFail($context['question']);
		$part = new Part();
		$mediaPath = Part::uploadImage($request, 'image');
		if ($mediaPath)
			FileLink::link($mediaPath);
		$part->image = $mediaPath;
		$part->key = $request->key;
		$part->question()->associate($question);
		$part->save();

		session()->put('success', "Изображение к вопросу № {$question->sort_no} создано");
		return redirect()->route('parts.index');
	}

	public function show(int $id) {
		return $this->edit($id, true);
	}

	public function edit(int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$part = Part::findOrFail($id);
		$keys = [];
		$temp = collect(explode(PHP_EOL, $part->question->kind->keys))
			->each(function ($key) use (&$keys) {
				$key = trim($key);
				$keys[$key] = $key;
			});
		return view('parts.edit', compact('mode', 'part', 'keys'));
	}

	public function update(UpdatePartRequest $request, int $id) {
		$context = session('context');
		$question = Question::findOrFail($context['question']);
		$part = Part::findOrFail($id);
		$mediaPath = Part::uploadImage($request, 'image', $part->image);
		if ($mediaPath) {
			FileLink::link($mediaPath);
			$part->image = $mediaPath;
		}
		$part->key = $request->key;
		$part->update();

		session()->put('success', "Изображение к вопросу № {$question->sort_no} обновлено");
		return redirect()->route('parts.index');
	}

	public function destroy(Request $request, int $part) {
		if ($part == 0) {
			$id = $request->id;
		} else
			$id = $part;

		$part = Part::findOrFail($id);
		$number = $part->getKey();
		FileLink::unlink($part->image);
		$part->delete();

		event(new ToastEvent('success', '', "Изображение № {$number} удалено"));
		return true;
	}
}