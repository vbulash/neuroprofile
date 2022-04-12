<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class OGRNControlSumRule implements Rule
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
        // Алгоритм
        // https://www.sberbank.ru/ru/s_m_business/pro_business/ogrn-i-ogrnip-chto-eto-takoe-i-kak-proverit-ogrn-po-inn/
        $baselen = strlen($value);
        $common = substr($value, 0, $baselen - 1);
        $control = substr($value, $baselen - 1, 1);
        //
        $step1 = intval($common) / ($baselen - 2);
        $step2 = intval($step1) * ($baselen - 2);;
        $step3 = (intval($common) - $step2) % 10;
        $step4 = ($step3 == $control);

        return $step4;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Проверьте ОГРН / ОГРНИП - контрольная сумма неверна';
    }
}
