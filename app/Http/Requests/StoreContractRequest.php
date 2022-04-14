<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
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
            'number' => 'required',
            'invoice' => 'required',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'license_count' => 'required|numeric',
            'url' => 'required|url'
        ];
    }

    public function attributes()
    {
        return [
            'number' => 'Номер контракта',
            'invoice' => 'Номер оплаченного счета',
            'start' => 'Дата начала контракта',
            'end' => 'Дата завершения контракта',
            'license_count' => 'Количество лицензий',
            'url' => 'URL страницы сайта клиента'
        ];
    }


}
