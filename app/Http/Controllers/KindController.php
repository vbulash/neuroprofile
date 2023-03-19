<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreKindRequest;
use App\Models\Kind;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class KindController extends Controller {
	public function getData(): JsonResponse {
		$kinds = Kind::all();

		return DataTables::of($kinds)
			->addColumn('action', function ($kind) {
				$editRoute = route('kinds.edit', ['kind' => $kind->getKey()]);
				$showRoute = route('kinds.show', ['kind' => $kind->getKey()]);

				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-pencil-alt', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete($kind->getKey(), '$kind->name')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index() {
		$count = Kind::all()->count();
		return view('kinds.index', compact('count'));
	}

	public function create() {
		$mode = config('global.create');
		return view('kinds.create', compact('mode'));
	}

	public function store(StoreKindRequest $request) {
		$kind = new Kind();
		$kind->name = $request->name;
		$kind->images = $request->images;
		$kind->answers = $request->answers;
		$kind->keys = $request->keys;
		$kind->cue = $request->cue;
		$kind->phone = $request->phone;
		$kind->tablet = $request->tablet;
		$kind->desktop = $request->desktop;
		$kind->save();

		session()->put('success', "Тип вопросов &laquo;{$kind->name}&raquo; создан");
		return redirect()->route('kinds.index');
	}

	public function show($id) {
		return $this->edit($id, true);
	}

	public function edit(int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$kind = Kind::findOrFail($id);
		return view('kinds.edit', compact('mode', 'kind'));
	}

	public function update(StoreKindRequest $request, int $id) {
		$kind = Kind::findOrFail($id);
		$kind->update([
			'name' => $request->name,
			'images' => $request->images,
			'answers' => $request->answers,
			'keys' => $request->keys,
			'cue' => $request->cue,
			'phone' => $request->phone,
			'tablet' => $request->tablet,
			'desktop' => $request->desktop,
		]);

		session()->put('success', "Тип вопросов &laquo;{$kind->name}&raquo; обновлён");
		return redirect()->route('kinds.index');
	}

	public function destroy(Request $request, int $kind) {
		if ($kind == 0) {
			$id = $request->id;
		} else
			$id = $kind;

		$kind = Kind::findOrFail($id);
		$name = $kind->name;
		$kind->delete();

		event(new ToastEvent('success', '', "Тип вопросов &laquo;{$name}&raquo; удалён"));
		return true;
	}
}