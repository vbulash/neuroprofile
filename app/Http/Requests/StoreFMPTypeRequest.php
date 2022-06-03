<?php

namespace App\Http\Requests;

use App\Rules\OneEthalonRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFMPTypeRequest extends FormRequest
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
				'numeric'
			],
			'ethalon' => new OneEthalonRule($this)
		];
	}

	public function attributes()
	{
		return [
			'name' => 'Наименование',
			'limit' => 'Необходимо нейропрофилей',
			'ethalon' => 'Эталонный тип описания'
		];
	}

	/*
	 *
	 */
}
