<?php

namespace App\Http\Controllers\tests;

use App\Models\Set;
use App\Models\Test;
use App\Models\TestOptions;
use Illuminate\Http\Request;

class StepMechanics implements Step
{
    public function getTitle(): string
    {
        return 'Набор вопросов и дополнительные механики';
    }

    public function store(array $data): bool
	{
		$heap = session('heap');
		$heap['step-mechanics'] = true;
		$heap['set_id'] = $data['set_id'];
		$options = intval($heap['options']);
		if (isset($data['eye']))
			$options |= TestOptions::EYE_TRACKING->value;
		if (isset($data['mouse']))
			$options |= TestOptions::MOUSE_TRACKING->value;
		$heap['options'] = $options;
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
		$test = $request->test;
		$mode = intval($request->mode);
		$buttons = intval($request->buttons);
		$sets = Set::all()
			->sortBy('name')
			->pluck(value: 'name', key: 'id')
			->all()
		;

		if ($mode != config('global.create')) {
			$heap = session('heap');
			$test = Test::findOrFail($request->test);
			if (!isset($heap['step-mechanics'])) {
				//$content = json_decode($test->content, true);
				$heap['step-mechanics'] = true;
				$heap['set_id'] = $test->set->getKey();
				$heap['options'] = $test->options;
				session()->put('heap', $heap);
			}
			$test = $test->getKey();
		} else $test = $request->test;

		return view('tests.steps.mechanics', compact('mode', 'buttons', 'sets', 'test'));
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
