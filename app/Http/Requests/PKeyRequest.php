<?php

namespace App\Http\Requests;

use App\Rules\PKeyRule;
use Illuminate\Foundation\Http\FormRequest;

class PKeyRequest extends FormRequest
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
            'pkey' => [
                'required',
                new PKeyRule()
            ]
        ];
    }

    public function attributes()
    {
        return [
            'pkey' => 'Персональный ключ'
        ];
    }
}
