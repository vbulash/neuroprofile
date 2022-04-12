<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'fio' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255'],
			'password' => ['confirmed'],
        ];
    }

	public function attributes()
	{
		return [
			'fio' => 'Фамилия, имя и отчество',
			'email' => 'Электронная почта',
			'password' => 'Пароль',
		];
	}
}
