<?php

namespace App\Http\Controllers\tests;

use App\Models\Contract;
use App\Models\TestOptions;
use Illuminate\Http\Request;

class StepCore implements Step
{
    public function getTitle(): string
    {
        return 'Основная информация';
    }

    public function store(Request $request): bool
	{
		$data = $request->except(['_token', '_method', 'mode', 'test']);
		$heap = session('heap') ?? [];
		$heap['step-core'] = $data['step-core'];
		$heap['name'] = $data['name'];
		$heap['contract_id'] = $data['contract_id'];
		$heap['paid'] = isset($data['paid']);
		$options = $heap['options'] ?? 0;
		$options &= ~(
			TestOptions::AUTH_PKEY->value |
			TestOptions::AUTH_GUEST->value |
			TestOptions::AUTH_MIX->value |
			TestOptions::AUTH_FULL->value
		);
		$options |= intval($data['auth']);
		$heap['options'] = $options;
		session()->put('heap', $heap);

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
		$contracts = Contract::all()
			->mapWithKeys(fn ($contract) =>
			[$contract->getKey() => sprintf("%s (%s)", $contract->number, $contract->client->name )])
			->toArray();

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
