<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class NewUserRequest extends FormRequest
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
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password' => ['required', 'confirmed'],
			'role' => ['required'],
			'terms' => ['required']
		];
    }

	public function attributes()
	{
		return [
			'name' => 'Фамилия, имя и отчество',
			'email' => 'Адрес электронной почты',
			'password' => 'Пароль',
			'role' => 'Роль нового пользователя',
			'terms' => 'Согласие с Политикой конфиденциальности'
		];
	}
}
