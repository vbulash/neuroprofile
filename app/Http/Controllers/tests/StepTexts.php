<?php

namespace App\Http\Controllers\tests;

use App\Models\FileLink;
use App\Models\FMPType;
use App\Models\Set;
use App\Models\Test;
use App\Models\TestOptions;
use Illuminate\Http\Request;

class StepTexts implements Step {
	public function getTitle(): string {
		return 'Настраиваемые тексты';
	}

	public function store(Request $request): bool {
		$data = $request->except(['_token', '_method', 'mode', 'test']);
		$heap = session('heap') ?? [];
		$heap['step-texts'] = $data['step-texts'];
		$options = intval($heap['options'] ?? 0);
		if (isset($data['texts-option'])) {
			$options |= TestOptions::CUSTOM_TEXTS->value;
			$heap['texts']['pretext'] = $data['pretext'];
			$heap['texts']['posttext'] = $data['posttext'];
			$heap['options'] = $options;
		} else
			unset($heap['texts']);
		session()->put('heap', $heap);

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

		return view('tests.steps.texts', compact('mode', 'buttons', 'test'));
	}

	public function getStoreRules(): array {
		return [];
	}

	public function getStoreAttributes(): array {
		return [];
	}
}