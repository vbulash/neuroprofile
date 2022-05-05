<?php

namespace App\Http\Requests;

use App\Rules\ProfileCount;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFMPTypeRequest extends FormRequest
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
			'name' => 'required',
			'limit' => [
				'required',
				'numeric',
				new ProfileCount($this)	// Новый план нейропрофилей не может быть больше факта
			]
		];
	}

	public function attributes()
	{
		return [
			'name' => 'Наименование',
			'limit' => 'Необходимо нейропрофилей',
		];
	}

	/*
	 *
	 */
}
