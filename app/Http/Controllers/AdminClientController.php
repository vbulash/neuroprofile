<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\StoreClientAdminRequest;
use App\Http\Requests\UpdateAdminClientRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewUser;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class AdminClientController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$admins = User::whereHas("roles", fn($query) => $query->where("name", RoleName::CLIENT_ADMIN->value))->get();
		return Datatables::of($admins)
			->addColumn('clients', function ($admin) {
				$clients = $admin->clients->pluck('name')->toArray();
				return count($clients) == 0 ? null : json_encode($clients);
			})
			// ->editColumn('role', fn($admin) => $admin->getRoleNames()->toArray())
			->addColumn('action', function ($admin) {
				$editRoute = route('adminclients.edit', ['adminclient' => $admin->id]);
				$showRoute = route('adminclients.show', ['adminclient' => $admin->id]);
				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete($admin->id, '$admin->name')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];
				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index() {
		$count = User::all()->count();
		return view('adminclients.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create() {
		$mode = config('global.create');
		return view('adminclients.create', compact('mode'));
	}

	public function store(StoreClientAdminRequest $request) {
		$admin = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
		]);

		$admin->assignRole(RoleName::CLIENT_ADMIN->value);
		if (isset($request->clients)) {
			$clients = json_decode($request->clients);
			$admin->clients()->sync($clients);
		}

		event(new Registered($admin));
		$admin->notify(new NewUser());
		$name = $admin->name;

		session()->put('success',
			"Зарегистрирован новый аккаунт менеджер \"{$name}\"");
		return redirect()->route('adminclients.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id) {
		$mode = config('global.show');
		$admin = User::findOrFail($id);
		$clients = $admin->clients->pluck('id')->toArray();
		$allclients = Client::all()
			->sortBy('name')
			->pluck('name', 'id')->toArray();
		return view('adminclients.show', compact('admin', 'mode', 'clients', 'allclients'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function edit(Request $request, int $id) {
		$mode = config('global.edit');
		$admin = User::findOrFail($id);
		$clients = $admin->clients->pluck('id')->toArray();
		$allclients = Client::all()
			->sortBy('name')
			->pluck('name', 'id')->toArray();
		return view('adminclients.edit', compact('admin', 'mode', 'allclients', 'clients'));
	}

	public function update(UpdateAdminClientRequest $request, $id) {
		$admin = User::findOrFail($id);
		$name = $admin->name;
		$draft = $request->except('_token');
		if ($request->has('password'))
			$draft['password'] = Hash::make($request->password);
		$admin->update($draft);

		if (isset($request->clients)) {
			$clients = json_decode($request->clients);
			$admin->clients()->sync($clients);
		}
		// $admin->save();

		session()->put('success',
			"Аккаунт менеджер \"{$name}\" обновлён");

		return redirect()->route('adminclients.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $admin
	 * @return bool
	 */
	public function destroy(Request $request, int $admin) {
		if ($admin == 0) {
			$id = $request->id;
		} else
			$id = $admin;

		$admin = User::findOrFail($id);
		$name = $admin->name;
		$admin->delete();

		event(new ToastEvent('success', '', "Аккаунт менеджер '{$name}' удалён"));
		return true;
	}
}