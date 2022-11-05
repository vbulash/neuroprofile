<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKindRequest extends FormRequest {
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
	 * @return array<string, mixed>
	 */
	public function rules() {
		return [
			'name' => 'required',
			'images' => ['required', 'numeric'],
			'answers' => ['required', 'numeric'],
			'keys' => ['required']
		];
	}

	public function attributes() {
		return [
			'name' => 'Название',
			'images' => 'Количество изображений вопроса',
			'answers' => 'Количество ответов в вопросе',
			'keys' => 'Множество ключей вопросов',
		];
	}
}
