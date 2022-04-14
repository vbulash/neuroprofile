<?php

namespace App\Rules;

use App\Models\Contract;
use App\Models\License;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class LicenseCount implements Rule
{
	protected Request $request;
	protected int $count;

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
	 * @param string $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$id = $this->request->contract_id;
		$this->count = Contract::findOrFail($id)->licenses->count();
		return intval($value) >= $this->count;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return "Новое количество лицензий должно быть больше количества уже выпущенных ({$this->count})";
	}
}
