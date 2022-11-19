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

    public function store(Request $request): bool
	{
		$data = $request->except(['_token', '_method', 'mode', 'test']);
		$heap = session('heap') ?? [];
		$heap['step-payment'] = $data['step-payment'];
		$options = intval($heap['options'] ?? 0);
		if (isset($data['payment-option'])) {
			$options |= TestOptions::CUSTOM_PAYMENT->value;
			$heap['robokassa']['merchant'] = $data['merchant'];
			$heap['robokassa']['password'] = $data['password'];
			$heap['robokassa']['sum'] = $data['sum'];
			$heap['options'] = $options;
		} else unset($heap['robokassa']);
		session()->put('heap', $heap);
//		session()->keep('heap');

        return true;
    }

    public function update(Request $request): bool
    {
		return $this->store($request);
    }

	public function create(Request $request)
	{
		return $this->edit($request);
	}

	public function edit(Request $request)
	{
		$mode = intval($request->mode);
		$buttons = intval($request->buttons);
		$test = intval($request->test);

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
