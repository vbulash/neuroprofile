<?php

namespace App\Http\Controllers\tests;

use App\Models\FMPType;
use App\Models\Set;
use App\Models\Test;
use App\Models\TestOptions;
use Illuminate\Http\Request;

class StepResults implements Step {
	public function getTitle(): string {
		return 'Представление результатов тестирования';
	}

	public function store(Request $request): bool {
		$data = $request->except(['_token', '_method', 'mode', 'test']);
		$heap = session('heap') ?? [];
		$heap['step-results'] = $data['step-results'];
		$options = intval($heap['options'] ?? 0);
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
		if (!isset($data['show-name-option'])) {
			$results = true;
			$options |= TestOptions::DONT_SHOW_TITLE->value;
		} else {
			$options ^= TestOptions::DONT_SHOW_TITLE->value;
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
		//		session()->keep('heap');

		return true;
	}

	public function update(Request $request): bool {
		return $this->store($request);
	}

	public function create(Request $request) {
		return $this->edit($request);
	}

	public function edit(Request $request) {
		$mode = intval($request->mode);
		$buttons = intval($request->buttons);
		$test = intval($request->test);
		$fmptypes = FMPType::all()
			->where('active', true)
			->sortBy('name')
			->pluck(value: 'name', key: 'id')
			->all()
		;

		return view('tests.steps.results', compact('mode', 'buttons', 'fmptypes', 'test'));
	}

	public function getStoreRules(): array {
		return [];
	}

	public function getStoreAttributes(): array {
		return [];
	}
}