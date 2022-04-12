<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInternshipRequest extends FormRequest
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
			'iname' => 'required',
			'itype' => 'required',
			'status' => 'required',
			'program' => 'required',
        ];
    }

	public function attributes()
	{
		return [
			'iname' => 'Название стажировки',
			'itype' => 'Тип стажировки',
			'status' => 'Статус стажировки',
			'program' => 'Программа стажировки'
		];
	}
}
