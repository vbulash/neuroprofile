<?php

namespace App\Http\Controllers\tests;

use App\Models\FMPType;
use App\Models\Set;
use App\Models\Test;
use App\Models\TestOptions;
use Illuminate\Http\Request;

class StepResults implements Step
{
    public function getTitle(): string
    {
        return 'Представление результатов тестирования';
    }

    public function store(array $data): bool
	{
		$heap = session('heap');
		$heap['step-results'] = true;
		$options = intval($heap['options']);
		$results = false;
		$show = $mail = $client = 0;
		if (isset($data['show-option'])) {
			$results = true;
			$options |= TestOptions::RESULTS_SHOW->value;
			$show = $data['show'];
		}
		if (isset($data['mail-option'])) {
			$results = true;
			$options |= TestOptions::RESULTS_MAIL->value;
			$mail = $data['mail'];
		}
		if (isset($data['client-option'])) {
			$results = true;
			$options |= TestOptions::RESULTS_CLIENT->value;
			$client = $data['client'];
		}

		if ($results) {
			$heap['options'] = $options;
			$heap['descriptions'] = [];
			if ($show)
				$heap['descriptions']['show'] = $show;
			if ($mail)
				$heap['descriptions']['mail'] = $mail;
			if ($client)
				$heap['descriptions']['client'] = $client;
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
		$fmptypes = FMPType::all()
			->where('active', true)
			->sortBy('name')
			->pluck(value: 'name', key: 'id')
			->all()
		;
		if ($mode != config('global.create')) {
			$heap = session('heap');
			$test = Test::findOrFail($request->test);
			if (!isset($heap['step-payment'])) {
				$content = json_decode($test->content, true);
				$heap['step-results'] = true;
				$heap['options'] = $test->options;
				$heap['descriptions'] = $content['descriptions'];
				session()->put('heap', $heap);
			}
			$test = $test->getKey();
		} else $test = $request->test;

		return view('tests.steps.results', compact('mode', 'buttons', 'fmptypes', 'test'));
	}

	public function getStoreRules(): array
	{
		return [];
	}

	public function getStoreAttributes(): array
	{
		return [];
	}
}
