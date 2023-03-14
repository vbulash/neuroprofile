<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Admin;
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

class AdminController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$admins = User::whereHas("roles", fn($query) => $query->where("name", RoleName::ADMIN->value))->get();
		return Datatables::of($admins)
			->editColumn('role', fn($admin) => $admin->getRoleNames()->toArray())
			->addColumn('action', function ($admin) {
				$editRoute = route('admins.edit', ['admin' => $admin->id]);
				$showRoute = route('admins.show', ['admin' => $admin->id]);
				$actions = '';

				$actions .=
					"<a href=\"$editRoute\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-edit\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"$showRoute\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete($admin->id, '$admin->name')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";
				return $actions;
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
		return view('admins.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create() {
		$mode = config('global.create');
		return view('admins.create', compact('mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param UpdateUserRequest $request
	 * @return RedirectResponse
	 */
	public function store(UpdateUserRequest $request) {
		$admin = Admin::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
		]);
		$admin->assignRole(RoleName::ADMIN->value);

		event(new Registered($admin));
		$admin->notify(new NewUser());
		$name = $admin->name;

		session()->put('success',
			"Зарегистрирован новый администратор \"{$name}\"");
		return redirect()->route('admins.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id) {
		$mode = config('global.show');
		$admin = Admin::findOrFail($id);
		return view('admins.show', compact('admin', 'mode'));
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
		$profile = $request->has('profile');
		$roles = Role::all()->pluck('name')->toArray();
		return view('admins.edit', compact('admin', 'profile', 'roles', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateUserRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateUserRequest $request, $id) {
		$admin = User::findOrFail($id);
		$name = $admin->name;
		$draft = $request->except('_token');
		if ($request->has('password'))
			$draft['password'] = Hash::make($request->password);
		$admin->update($draft);
		$admin->save();

		session()->put('success',
			"Администратор \"{$name}\" обновлён");

		return redirect()->route('admins.index');
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

		event(new ToastEvent('success', '', "Администратор '{$name}' удалён"));
		return true;
	}
}