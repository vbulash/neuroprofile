<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            'learning' => 'required',
			'timeout' => [
				'required',
				'numeric'
			],
			'image1' => [
				'required',
				'image'
			],
			'image2' => [
				'required',
				'image'
			],
			'value1' => 'required',
			'value2' => 'required'
        ];
    }

	public function attributes()
	{
		return [
			'learning' => 'Режим прохождения',
			'timeout' => 'Таймаут прохождения вопроса',
			'image1' => 'Левая картинка вопроса',
			'image2' => 'Правая картинка вопроса',
			'value1' => 'Ключ левой картинки вопроса',
			'value2' => 'Ключ правой картинки вопроса'
		];
	}
}
