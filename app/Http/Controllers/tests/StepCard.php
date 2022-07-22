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

    public function store(Request $request): bool
	{
		$data = $request->except(['_token', '_method', 'mode', 'sid', 'test']);
		$heap = session('heap') ?? [];
		$heap['step-card'] = $data['step-card'];
		$heap['card'] = $data;
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
