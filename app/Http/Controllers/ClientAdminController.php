<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\StoreClientAdminRequest;
use App\Http\Requests\UpdateClientAdminRequest;
use App\Models\Client;
use App\Models\ClientAdmin;
use App\Models\User;
use App\Notifications\NewUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
		$admins = ClientAdmin::whereHas("roles", fn($query) => $query->where("name", RoleName::CLIENT_ADMIN->value))
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
		$admin = new ClientAdmin();
		$admin->name = $request->name;
		$admin->email = $request->email;
		$admin->password = Hash::make($request->password);
		$admin->save();
		$admin->clients()->attach($client);
		// $admin->save();
		$admin->assignRole(RoleName::CLIENT_ADMIN->value);

		event(new Registered($admin));
		$admin->notify(new NewUser());
		$name = $admin->name;

		session()->put('success',
			"Зарегистрирован новый администратор клиента \"{$name}\"");
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
		$admin = ClientAdmin::findOrFail($user);
		return view('clients.users.edit', compact('client', 'admin', 'mode'));
	}

	public function update(UpdateClientAdminRequest $request, int $client, int $user) {
		$client = Client::findOrFail($client);
		$admin = ClientAdmin::findOrFail($user);
		$name = $admin->name;

		$draft = [];
		$draft['name'] = $request->name;
		if ($request->has('password'))
			$draft['password'] = Hash::make($request->password);
		$admin->update($draft);

		session()->put('success', "Администратор клиента \"{$name}\" обновлён");

		return redirect()->route('clients.users.index', ['client' => $client->getKey()]);
	}

	public function destroy(Request $request, int $client, int $user) {
		if ($user == 0) {
			$id = $request->id;
		} else
			$id = $user;

		$admin = ClientAdmin::findOrFail($id);
		$name = $admin->name;
		$admin->delete();

		event(new ToastEvent('success', '', "Администратор клиента \"{$name}\" удалён"));
		return true;
	}

	public function attach(Request $request) {
		$client = Client::findOrFail($request->client);
		$client->users()->attach($request->user);
		return true;
	}

	public function detach(Request $request) {
		$client = Client::findOrFail($request->client);
		$client->users()->detach($request->user);
		return true;
	}
}