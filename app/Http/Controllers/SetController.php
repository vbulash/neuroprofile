<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreSetRequest;
use App\Http\Requests\UpdateSetRequest;
use App\Models\Client;
use App\Models\Set;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

class SetController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$sets = Set::all();

		return Datatables::of($sets)
			->editColumn('questions', function ($set) {
				$count = $set->questions->count();
				switch ($count) {
					case 0:
					case null:
						return 'Нет';
					default:
						return $count;
				}
			})
			->addColumn('action', function ($set) {
				$editRoute = route('sets.edit', ['set' => $set->getKey()]);
				$showRoute = route('sets.show', ['set' => $set->getKey()]);
				$selectRoute = route('sets.select', ['set' => $set->getKey()]);
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
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$set->getKey()}, '{$set->name}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
					"<i class=\"fas fa-check\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	public function select(int $id) {
		session()->put('context', ['set' => $id]);
		return redirect()->route('questions.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index() {
		session()->forget('context');
		$count = Set::all()->count();
		return view('sets.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create() {
		$mode = config('global.create');
		return view('sets.create', compact('mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreSetRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreSetRequest $request) {
		$set = new Set();
		$set->name = $request->name;
		$set->code = (env('EXEC_MODE') == 'research' ? '//' : $request->code);
		$set->save();
		$name = $set->name;

		session()->put('success', "Набор вопросов \"{$name}\" создан");
		return redirect()->route('sets.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Application|Factory|View
	 */
	public function show(int $id) {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$set = Set::findOrFail($id);
		return view('sets.edit', compact('set', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param  int  $id
	 * @return RedirectResponse
	 */
	public function update(UpdateSetRequest $request, int $id) {
		$set = Set::findOrFail($id);
		$name = $set->name;
		$set->update([
			'name' => $request->name,
			'code' => (env('EXEC_MODE') == 'research' ? '//' : $request->code)
		]);

		session()->put('success', "Набор вопросов \"{$name}\" обновлён");
		return redirect()->route('sets.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $set
	 * @return bool
	 */
	public function destroy(Request $request, int $set) {
		if ($set == 0) {
			$id = $request->id;
		} else
			$id = $set;

		$set = Set::findOrFail($id);
		$name = $set->name;
		$set->delete();

		event(new ToastEvent('success', '', "Набор вопросов '{$name}' удалён"));
		return true;
	}
}