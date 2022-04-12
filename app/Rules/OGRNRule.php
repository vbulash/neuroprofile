<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class OGRNRule implements Rule
{
    protected Request $request;

    /**
     * Create a new rule instance.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!is_numeric($value)) return false;   // Не цифры
        if(strlen($value) == 13) return true;   // Юридические лица
        if(strlen($value) == 15) return true;   // Индивидуальные предприниматели
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Основной государственный регистрационный номер должен состоять из 13 цифр (для юридических лиц) или 15 цифр (для ИП)';
    }
}
