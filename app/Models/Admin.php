<?php

namespace App\Models;

class Admin extends User implements FormTemplate {
	protected $table = 'users';

	public static function createTemplate(): array {
		return [
			'id' => 'admin-create',
			'name' => 'admin-create',
			'action' => route('admins.store'),
			'close' => route('admins.index'),
		];
	}

	public function editTemplate(): array {
		return [
			'id' => 'admin-edit',
			'name' => 'admin-edit',
			'action' => route('admins.update', ['admin' => $this->getKey()]),
			'close' => route('admins.index'),
		];
	}
}