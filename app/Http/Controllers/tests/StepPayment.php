<?php

namespace App\Http\Controllers\tests;

use App\Models\FMPType;
use App\Models\Set;
use App\Models\Test;
use App\Models\TestOptions;
use Illuminate\Http\Request;

class StepPayment implements Step
{
    public function getTitle(): string
    {
        return 'Настраиваемая оплата';
    }

    public function store(array $data): bool
	{
		$heap = session('heap');
		$heap['step-payment'] = true;
		$options = intval($heap['options']);
		if (isset($data['payment-option'])) {
			$options |= TestOptions::CUSTOM_PAYMENT->value;
			$heap['robokassa']['merchant'] = $data['merchant'];
			$heap['robokassa']['password'] = $data['password'];
			$heap['robokassa']['sum'] = $data['sum'];
			$heap['options'] = $options;
		}
		session()->put('heap', $heap);

        return true;
    }

    public function update(array $data): bool
    {
		return $this->store($data);
    }

	public function create(Request $request)
	{
		return $this->edit($request);
	}

	public function edit(Request $request)
	{
		$mode = intval($request->mode);
		$buttons = intval($request->buttons);

		if ($mode != config('global.create')) {
			$heap = session('heap');
			$test = Test::findOrFail($request->test);
			if (!isset($heap['step-payment'])) {
				$content = json_decode($test->content, true);
				$heap['step-payment'] = true;
				$heap['options'] = $test->options;
				if ($test->options & TestOptions::CUSTOM_PAYMENT->value)
					$heap['robokassa'] = $content['robokassa'];
				session()->put('heap', $heap);
			}
			$test = $test->getKey();
		} else $test = $request->test;

		return view('tests.steps.payment', compact('mode', 'buttons', 'test'));
	}

	public function getStoreRules(): array
	{
		return [
			'merchant' => 'required_with:payment-option',
			'password' => 'required_with:payment-option',
			'sum' => 'required_with:payment-option',
		];
	}

	public function getStoreAttributes(): array
	{
		return [
			'merchant' => 'Магазин Robokassa',
			'password' => 'Пароль магазина Robokassa',
			'sum' => 'Сумма оплаты за платный результат тестирования Robokassa',
			'payment-option' => 'Тест имеет самостоятельную оплату'
		];
	}
}
