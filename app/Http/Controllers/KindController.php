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

			    $actions = "<a href=\"$editRoute\" class=\"btn btn-primary btn-sm float-left me-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
			    	"<i class=\"fas fa-pencil-alt\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"$showRoute\" class=\"btn btn-primary btn-sm float-left me-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
			    	"<i class=\"fas fa-eye\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete($kind->getKey(), '$kind->name')\">\n" .
			    	"<i class=\"fas fa-trash-alt\"></i>\n" .
			    	"</a>\n";

			    return $actions;
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
			'keys' => $request->keys
		]);

		session()->put('success', "Тип вопросов &laquo;{$kind->name}&raquo; обновлён");
		return redirect()->route('kinds.index');
	}

	public function destroy(Request $request, int $kind)
	{
		if ($kind == 0) {
			$id = $request->id;
		} else $id = $kind;

		$kind = Kind::findOrFail($id);
		$name = $kind->name;
		$kind->delete();

		event(new ToastEvent('success', '', "Тип вопросов &laquo;{$name}&raquo; удалён"));
		return true;
	}
}
