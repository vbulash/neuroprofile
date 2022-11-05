<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSetRequest extends FormRequest
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
		$rules = ['name' => 'required'];
		if (!env('RESEARCH'))
			$rules['code'] = 'required';
        return $rules;
    }

	public function attributes()
	{
		$attributes = ['name' => 'Наименование набора вопросов'];
		if (!env('RESEARCH'))
			$attributes['code'] = 'PHP-код вычисления кода нейропрофиля';
		return $attributes;
	}
}
