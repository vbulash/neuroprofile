<?php

namespace App\Http\Controllers\tests;

use App\Models\Contract;
use App\Models\Test;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StepCore implements Step
{
    public function getTitle(): string
    {
        return 'Основная информация';
    }

    public function store(array $data): bool
	{
		$heap = [
			'step-core' => true,
			'name' => $data['name'],
			'contract_id' => $data['contract_id'],
			'options' => intval($data['auth']),
			'paid' => isset($data['paid'])
		];
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
		$contracts = Contract::all()
			->mapWithKeys(fn ($contract) =>
			[$contract->getKey() => sprintf("%s (%s)", $contract->number, $contract->client->name )])
			->toArray();

		if ($mode != config('global.create')) {
			$test = Test::findOrFail($request->test);
			if (!isset($heap['step-card'])) {
				$heap = [
					'step-core' => true,
					'name' => $test->name,
					'key' => $test->key,
					'contract_id' => $test->contract->getKey(),
					'options' => $test->options,
					'paid' => $test->paid
				];
				session()->put('heap', $heap);
			}
			$test = $test->getKey();
		} else $test = $request->test;
		return view('tests.steps.core', compact('mode', 'contracts', 'buttons', 'test'));
	}

	public function getStoreRules(): array
	{
		return [
			'name' => 'required',
		];
	}

	public function getStoreAttributes(): array
	{
		return [
			'name' => 'Название теста',
		];
	}
}
