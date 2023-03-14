<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClientAdmin extends User implements FormTemplate {
	protected $table = 'users';

	protected $guard_name = 'web';

	public function clients(): BelongsToMany {
		return $this->belongsToMany(Client::class, 'users_clients', 'user_id', 'client_id')
			->withTimestamps();
	}

	public static function createTemplate(): array {
		$context = session('context');
		return [
			'id' => 'client-admin-create',
			'name' => 'client-admin-create',
			'action' => route('clients.users.store', ['client' => $context['client']]),
			'close' => route('clients.users.index', ['client' => $context['client']]),
		];
	}

	public function editTemplate(): array {
		$context = session('context');
		return [
			'id' => 'cient-admin-edit',
			'name' => 'client-admin-edit',
			'action' => route('clients.users.update', ['client' => $context['client'], 'user' => $this->getKey()]),
			'close' => route('clients.users.index', ['client' => $context['client']]),
		];
	}
}