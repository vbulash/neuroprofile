<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		// Reset cached roles and permissions
		app()[PermissionRegistrar::class]->forgetCachedPermissions();

		$arrayOfPermissionNames = [
			//
			'users.list',
			'users.create',
			'users.edit',
			'users.show',
			'users.delete',
			//
			'clients.list',
			'clients.create',
			'clients.edit',
			'clients.show',
			'clients.delete',
			//
			'contracts.list',
			'contracts.create',
			'contracts.edit',
			'contracts.show',
			'contracts.delete',
		];
		$permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
			return ['name' => $permission, 'guard_name' => 'web'];
		});
		Permission::insert($permissions->toArray());

		/*
		 * Наполнение ролей
		 * TODO Раскрыть, когда будет серьезно прорабатываться система ролей платформы
		$employer = Role::where('name', 'Работодатель')->first();
		$employer->givePermissionTo([
			'employers.create',
			// При создании записи работодателя будет добавлены права на конкретный ID
			//'employers.edit',
			//'employers.show',,
		]);
		*/
    }
}
