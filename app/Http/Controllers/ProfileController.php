<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\FMPType;
use App\Models\Profile;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ProfileController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$context = session('context');
		$fmptype = FMPType::findOrFail($context['fmptype']);
		$profiles = $fmptype->profiles;

		return Datatables::of($profiles)
			->addColumn('fact', fn($profile) => $profile->blocks->count())
			->addColumn('action', function ($profile) {
				$editRoute = route('profiles.edit', ['profile' => $profile->getKey()]);
				$showRoute = route('profiles.show', ['profile' => $profile->getKey()]);
				$selectRoute = route('profiles.select', ['profile' => $profile->getKey()]);

				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-pencil-alt', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete({$profile->getKey()}, '{$profile->name}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Блоки описания'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(int $id) {
		$context = session('context');
		$context['profile'] = $id;
		session()->put('context', $context);

		return redirect()->route('blocks.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index() {
		$context = session('context');
		unset($context['profile']);
		unset($context['block']);
		session()->put('context', $context);

		$fmptype = FMPType::findOrFail($context['fmptype']);
		$count = $fmptype->profiles->count();
		$codeCount = count(Profile::getFreeCodes($fmptype->getKey()));
		return view('profiles.index', compact('count', 'codeCount'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create() {
		$mode = config('global.create');
		$context = session('context');
		$fmptype = FMPType::findOrFail($context['fmptype']);
		$codes = Profile::getFreeCodes($fmptype->getKey());
		return view('profiles.create', compact('fmptype', 'mode', 'codes'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreProfileRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreProfileRequest $request) {
		$name = '';
		DB::transaction(function () use ($request, &$name) {
			$context = session('context');

			$profile = Profile::create($request->except('_token'));
			$profile->save();

			$fmptype = FMPType::findOrFail($context['fmptype']);
			$count = $fmptype->profiles->count();
			$fmptype->update([
				'active' => intval($fmptype->limit) == $count
			]);

			$name = $profile->name;
		});

		session()->put('success', "Нейропрофиль \"{$name}\" создан");
		return redirect()->route('profiles.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id) {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$profile = Profile::findOrFail($id);
		$context = session('context');
		$codes = Profile::getAllCodes($context['fmptype']);
		return view('profiles.edit', compact('profile', 'mode', 'codes'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateProfileRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateProfileRequest $request, $id) {
		$profile = Profile::findOrFail($id);
		$name = $profile->name;
		$profile->update($request->except('_token'));

		session()->put('success', "Нейропрофиль \"{$name}\" обновлён");
		return redirect()->route('profiles.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $profile
	 * @return bool
	 */
	public function destroy(Request $request, int $profile) {
		if ($profile == 0) {
			$id = $request->id;
		} else
			$id = $profile;

		$name = '';
		DB::transaction(function () use ($id, &$name) {
			$profile = Profile::findOrFail($id);
			$name = $profile->name;
			$profile->delete();

			$context = session('context');
			$fmptype = FMPType::findOrFail($context['fmptype']);
			$count = $fmptype->profiles->count() - 1; // -1 - в рамках транзакции $profile->delete() не срабатывает мгноваенно
			$fmptype->update([
				'active' => intval($fmptype->limit) == $count
			]);
		});

		event(new ToastEvent('success', '', "Нейропрофиль '{$name}' удалён"));
		return true;
	}
}