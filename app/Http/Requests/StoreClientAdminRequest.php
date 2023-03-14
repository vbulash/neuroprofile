<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientAdminRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => [
				'required',
				'string', 'max:255',
				'unique:users,name'
			],
			'email' => [
				'required',
				'string', 'email',
				'unique:users,email',
				'max:255'
			],
			'password' => ['confirmed'],
		];
	}

	public function attributes() {
		return [
			'name' => 'Фамилия, имя и отчество',
			'email' => 'Электронная почта',
			'password' => 'Пароль',
		];
	}
}