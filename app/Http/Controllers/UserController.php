<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Employer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use \Exception;

class UserController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData()
	{
		return Datatables::of(User::all())
			->addColumn('action', function ($user) {
				$editRoute = route('users.edit', ['user' => $user->id, 'sid' => session()->getId()]);
				$showRoute = route('users.show', ['user' => $user->id, 'sid' => session()->getId()]);
				$actions = '';

				if (Auth::user()->can('users.edit'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('users.show'))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('users.destroy') && Auth::user()->getKey() != $user->getKey())
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$user->id}, '{$user->name}')\">\n" .
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
	public function index()
	{
		$count = User::all()->count();
		return view('users.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return RedirectResponse
	 */
	public function create()
	{
		// TODO Сделать полноценное создание пользователя по образцу автономного
		return redirect()->route('register', ['sid' => session()->getId()]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id)
	{
		$user = User::findOrFail($id);
		return view('users.show', compact('user'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function edit(Request $request, int $id)
	{
		$user = User::findOrFail($id);
		$profile = $request->has('profile');
		$roles = Role::all()->pluck('name')->toArray();
		return view('users.edit', compact('user', 'profile', 'roles'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateUserRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateUserRequest $request, $id)
	{
		$profile = $request->profile;
		$draft = [
			'name' => $request->fio,
			'email' => $request->email,
		];
		if ($request->password)
			$draft['password'] = Hash::make($request->password);
		$user = User::findOrFail($id);
		$name = $user->name;
		$user->update($draft);

		$user->roles()->detach();
		$user->assignRole($request->role);

		session()->put('success', "Пользователь \"{$name}\" обновлён");
		return redirect()->route($profile ? 'dashboard' : 'users.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $user
	 * @return bool
	 */
	public function destroy(Request $request, int $user)
	{
		if ($user == 0) {
			$id = $request->id;
		} else $id = $user;

		$user = User::findOrFail($id);
		$name = $user->name;
		$user->delete();

		event(new ToastEvent('success', '', "Пользователь '{$name}' удалён"));
		return true;
	}
}
