<?php

namespace App\Http\Controllers\tests;

use App\Models\FileLink;
use App\Models\FMPType;
use App\Models\Set;
use App\Models\Test;
use App\Models\TestOptions;
use Illuminate\Http\Request;

class StepBranding implements Step {
	public function getTitle(): string {
		return 'Настраиваемый брендинг';
	}

	public function store(Request $request): bool {
		$data = $request->except(['_token', '_method', 'mode', 'test']);
		$heap = session('heap') ?? [];
		$heap['step-branding'] = $data['step-branding'];
		$options = intval($heap['options'] ?? 0);
		if (isset($data['branding-option'])) {
			$options |= TestOptions::CUSTOM_BRANDING->value;
			if (isset($data['logo-file'])) {
				$mediaPath = Test::uploadImage($request, 'logo-file');
				if ($mediaPath)
					FileLink::link($mediaPath);
				$heap['branding']['logo'] = $mediaPath;
			} elseif ($data['clear-logo'] == 'true' && isset($heap['branding']['logo']))
				unset($heap['branding']['logo']);

			if ($data['background-input'])
				$heap['branding']['background'] = $data['background-input'];
			if ($data['font-color-input'])
				$heap['branding']['fontcolor'] = $data['font-color-input'];
			$heap['branding']['company-name'] = $data['company-name-changer'];
			$heap['branding']['signature'] = $data['signature'];
			$heap['options'] = $options;
		} else
			unset($heap['branding']);
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

		return view('tests.steps.branding', compact('mode', 'buttons', 'test'));
	}

	public function getStoreRules(): array {
		return [];
	}

	public function getStoreAttributes(): array {
		return [];
	}
}