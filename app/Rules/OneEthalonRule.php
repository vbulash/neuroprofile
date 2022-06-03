<?php

namespace App\Rules;

use App\Models\FMPType;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;

class OneEthalonRule implements Rule
{
	protected Request $request;

    /**
     * Create a new rule instance.
     *
     * @return void
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
		$fmptypes = FMPType::where('ethalon', true);
		if ($fmptypes->count() == 0) return true;
		if ($fmptypes->count() > 1) return false;
		if ($this->request->has('id')) {	// Редактирование типа описания
			$fmptype = $fmptypes->first();
			if ($fmptype->getKey() == $this->request->id) return true;
		}
		return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Эталонный тип описания может быть только один в списке типов описания';
    }
}
