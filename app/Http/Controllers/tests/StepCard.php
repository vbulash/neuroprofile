<?php

namespace App\Http\Controllers\tests;

use App\Models\Test;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StepCard implements Step
{
    public function getTitle(): string
    {
        return 'Конструктор анкеты';
    }

    public function store(array $data): bool
	{
		$heap = session('heap');
		$heap['step-card'] = true;
		$heap['card'] = $data;
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
			if (!isset($heap['step-card'])) {
				$content = json_decode($test->content, true);

				$heap['step-card'] = true;
				$heap['card'] = $content['card'];
				session()->put('heap', $heap);
			}
			$test = $test->getKey();
		} else $test = $request->test;

		return view('tests.steps.card', compact('mode', 'buttons', 'test'));
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
