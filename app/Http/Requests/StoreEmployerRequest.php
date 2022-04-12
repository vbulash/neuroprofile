<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployerRequest extends FormRequest
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
			'name' => ['required'],
			'phone' => ['required'],
			'email' => ['email', 'required'],
			'inn' => ['numeric', 'required'],
			'post_address' => ['required']
        ];
    }

	public function attributes()
	{
		return [
			'name' => 'Наименование организации',
			'phone' => 'Телефон',
			'email' => 'Электронная почта',
			'inn' => 'Индивидуальный номер налогоплательщика (ИНН)',
			'post_address' => 'Почтовый адрес'
		];
	}
}
