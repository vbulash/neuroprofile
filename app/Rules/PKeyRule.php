<?php

namespace App\Rules;

use App\Models\License;
use Illuminate\Contracts\Validation\Rule;

class PKeyRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pkey = License::all()->where('pkey', $value)->where('status', License::FREE)->first();
        return $pkey != null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Нет свободных лицензий, соответствующих введенному персональному ключу';
    }
}
