<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\StoreClientAdminRequest;
use App\Http\Requests\UpdateClientAdminRequest;
use App\Models\Client;
use App\Models\User;
use App\Notifications\NewUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class ClientAdminController extends Controller {
	public function getData(int $client): JsonResponse {
		$_client = Client::findOrFail($client);
		$users = $_client->users;

		return DataTables::of($users)
			->addColumn('action', function ($user) use ($_client) {
				$editRoute = route('clients.users.edit', [
					'client' => $_client->getKey(),
					'user' => $user->getKey()
				]);
				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item',
					'click' => "clickDetach({$_client->getKey()}, {$user->getKey()}, '{$user->getTitle()}')", 'icon' => 'fas fa-cancel', 'title' => 'Отмена привязки'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(int $client) {
		$client = Client::findOrFail($client);
		$count = $client->users()->count();

		$selected = $client->users->pluck('name', 'id')->toArray();
		$enabled = [];
		$admins = User::whereHas("roles", fn($query) => $query->where("name", RoleName::CLIENT_ADMIN->value))
			->get()
			->each(function ($admin) use (&$enabled, $selected) {
				$id = $admin->getKey();
				if (array_key_exists($id, $selected))
					return;
				$enabled[$id] = $admin->name;
			});

		return view('clients.users.index', compact('count', 'client', 'enabled', 'selected'));
	}

	public function create(int $client) {
		$mode = config('global.create');
		$client = Client::findOrFail($client);

		return view('clients.users.create', compact('mode', 'client'));
	}

	public function store(StoreClientAdminRequest $request, int $client) {
		// $client = Client::findOrFail($client);
		$admin = new User();
		$admin->name = $request->name;
		$admin->email = $request->email;
		$admin->password = Hash::make($request->password);
		$admin->save();
		$admin->clients()->attach($client);
		// $admin->save();

		$role = Role::findByName(RoleName::CLIENT_ADMIN->value);
		$admin->assignRole($role);
		$this->addPermissions($client, $admin);

		event(new Registered($admin));
		$admin->notify(new NewUser());
		$name = $admin->name;

		session()->put('success',
			"Зарегистрирован новый аккаунт менеджер \"{$name}\"");
		return redirect()->route('clients.users.index', ['client' => $client]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	public function edit(int $client, int $user) {
		$mode = config('global.edit');
		$client = Client::findOrFail($client);
		$admin = User::findOrFail($user);
		return view('clients.users.edit', compact('client', 'admin', 'mode'));
	}

	public function update(UpdateClientAdminRequest $request, int $client, int $user) {
		$client = Client::findOrFail($client);
		$admin = User::findOrFail($user);
		$name = $admin->name;

		$draft = [];
		$draft['name'] = $request->name;
		if ($request->has('password'))
			$draft['password'] = Hash::make($request->password);
		$admin->update($draft);

		session()->put('success', "Аккаунт менеджер \"{$name}\" обновлён");

		return redirect()->route('clients.users.index', ['client' => $client->getKey()]);
	}

	public function destroy(Request $request, int $client, int $user) {
		if ($user == 0) {
			$id = $request->id;
		} else
			$id = $user;

		$admin = User::findOrFail($id);
		$name = $admin->name;
		$this->revokePermissions($client, $admin);
		$admin->delete();

		event(new ToastEvent('success', '', "Аккаунт менеджер \"{$name}\" удалён"));
		return true;
	}

	public function attach(Request $request) {
		$client = Client::findOrFail($request->client);
		$user = User::findOrFail($request->user);
		$client->users()->attach($user);
		$this->addPermissions($request->client, $user);
		return true;
	}

	public function detach(Request $request) {
		$client = Client::findOrFail($request->client);
		$user = User::findOrFail($request->user);
		$client->users()->detach($user);
		$this->revokePermissions($request->client, $user);
		return true;
	}

	private function addPermissions(int $client, User $admin) {
		collect([
			//
			'clients.edit',
			'clients.show',
			//
			'contracts.list',
			'contracts.edit',
			'contracts.show',
			//
			'test.results',
			'respondent.info'
		])->map(function ($item) use ($admin, $client) {
			$permission = Permission::findOrCreate($item . '.' . $client, 'web');
			$admin->givePermissionTo($permission);
		});
	}

	private function revokePermissions(int $client, User $admin) {
		collect([
			//
			'clients.edit',
			'clients.show',
			//
			'contracts.list',
			'contracts.edit',
			'contracts.show',
			//
			'test.results',
			'respondent.info'
		])->map(function ($item) use ($admin, $client) {
			$permission = Permission::findOrCreate($item . '.' . $client, 'web');
			$admin->revokePermissionTo($permission);
		});
	}
}