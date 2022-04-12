<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
			'name' => 'Булаш Валерий',
			'email' => 'vbulash@yandex.ru',
			'password' => Hash::make('AeebIex1'),
			'remember_token' => 'uTwAjXQctZMJ7MefACmuduEjK5H9kDFQSUDaKNK500AbUGW5N8kY8y30FhcL'
		]);
		$user->assignRole('Администратор');

		$user = User::create([
			'name' => 'Владимир Козлов',
			'email' => 'kozlov@personahuman.ru',
			'password' => '$2y$10$ZRhSdXVEW8ojDkOAtgmOMe3Dt6XFrfF/DQvHmyGI2kYrWmSaJ6KI6',
			'remember_token' => ''
		]);
		$user->assignRole('Администратор');

		$user = User::create([
			'name' => 'Юлия Камалова',
			'email' => 'yuliyakamalova@inbox.ru',
			'password' => '$2y$10$SJtP2nh7vPeOUT1i7bJ1VOOES4U1EQoHS06Vkcifduk.cVBbV4Cee'
		]);
		$user->assignRole('Администратор');

		$user = User::create([
			'name' => 'Тамара Николаевна Мдинарадзе',
			'email' => 'mdinaradze@personahuman.ru',
			'password' => '$2y$10$vhf/cd36z2ZAJZU3Z9mP/u4OPU2njXOA.Nvjt0yhgP8gj98sebXtC',
			'remember_token' => 'ZXij5aDBBF485vTJhqaBr8p7IZLjYcXpExy0XzGv3l74A2MBpVfy6jBaR1y9'
		]);
		$user->assignRole('Администратор');
    }
}
