<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \Exception;
use Spatie\Permission\Models\Permission;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		$roles = Role::where('selfassign', true)
			->orderBy('name')
			->pluck('name')
			->toArray();
        return view('auth.register', ['roles' => $roles]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(NewUserRequest $request)
    {
		$role = $request->role;
		try {
			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => Hash::make($request->password),
			]);
			$user->assignRole($role);
			if($request->role == 'Работодатель') {
				$this->addWildcard($user, 'employers.edit', $user->getKey());
				$this->addWildcard($user, 'employers.show', $user->getKey());
			} elseif ($request->role == 'Практикант') {
				$this->addWildcard($user, 'students.edit', $user->getKey());
				$this->addWildcard($user, 'students.show', $user->getKey());
			}

			event(new Registered($user));
			$user->notify(new NewUser($user));
			$name = $user->name;

			Auth::login($user);

			session()->put('success',
				"Зарегистрирован новый пользователь \"{$name}\" с ролью \"{$role}\"");

			return redirect()->route('dashboard');
		} catch (Exception $exc) {
			session()->put('error',
				"Ошибка регистрации нового пользователя: {$exc->getMessage()}");

			return redirect()->route('register');
		}
    }

	private function addWildcard(User $user, string $right, int $id)
	{
		if ($user->hasPermissionTo($right)) {
			$permission = "{$right}.{$id}";
			Permission::findOrCreate($permission);
			$user->givePermissionTo($permission);
		}
	}
}
