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

    public function store(Request $request): bool
	{
		$data = $request->except(['_token', '_method', 'mode', 'test']);
		$heap = session('heap') ?? [];
		$heap['set_id'] = $data['set_id'];
		$heap['step-mechanics'] = $data['step-mechanics'];
		$options = intval($heap['options'] ?? 0);
		if (isset($data['face']))
			$options |= TestOptions::FACE_NEURAL->value;
		if (isset($data['eye']))
			$options |= TestOptions::EYE_TRACKING->value;
		if (isset($data['mouse']))
			$options |= TestOptions::MOUSE_TRACKING->value;
		$heap['options'] = $options;
		$heap['cue'] = $data['cue'];
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
		$test = $request->test;
		$mode = intval($request->mode);
		$buttons = intval($request->buttons);
		$test = intval($request->test);
		$sets = Set::all()
			->sortBy('name')
			->pluck(value: 'name', key: 'id')
			->all()
		;

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
