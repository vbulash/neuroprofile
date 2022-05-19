@extends('layouts.wizard')

@section('steps')
	@php
		$index = 0;
		$steps = [];
		foreach (\App\Http\Controllers\tests\StepController::$steps as $stepClass) {
            $step = new $stepClass();
            $steps[] = [
                'title' => $step->getTitle(),
                'active' => ($index == \App\Http\Controllers\tests\StepController::getCurrentStep()),
                'context' => 'wizard' . $index++,
			];
		}
	@endphp
@endsection

@section('form.params')
	id="core-create" name="core-create"
	@if ($buttons & \App\Http\Controllers\tests\WizardButtons::NEXT->value)
		action="{{ route('steps.next') }}"
	@elseif ($buttons & \App\Http\Controllers\tests\WizardButtons::FINISH->value)
		action="{{ route('steps.finish') }}"
	@endif
@endsection

@section('form.close')
	{{ route('steps.close', ['sid' => session()->getId()]) }}
@endsection

@section('form.back')
	@php
		$params = [
			'mode' => $mode,
			'test' => $test,
			'sid' => $sid
		];
        if (isset($test))
            $params['test'] = $test;
	@endphp
	{{ route('steps.back', $params) }}
@endsection

