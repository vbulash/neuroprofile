<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class HelperController extends Controller
{
	public function generatePassword(int $length): array
	{
		return [
			'password' => Str::random($length)
		];
	}
}
