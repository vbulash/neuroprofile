@extends('tests.steps.wizard')

@section('service')
	@if ($mode == config('global.create'))
		Создание теста
	@else
		@php
			$heap = session('heap');
		@endphp
		@if ($mode == config('global.show'))
			Просмотр
		@else
			Редактирование
		@endif теста &laquo;{{ $heap['name'] }}&raquo;
	@endif
@endsection

@section('interior.subheader') @endsection

@section('form.fields')
	@php
        $heap = session('heap');
        $options = intval($heap['options'] ?? 0);
        if (!isset($heap['step-mechanics']) && $mode == config('global.create')) {
            $fields = [
				['name' => 'set_id', 'title' => 'Набор вопросов', 'required' => false, 'type' => 'select', 'options' => $sets],
				['title' => 'Дополнительные механики нейротеста', 'type' => 'heading'],
				['name' => 'eye', 'title' => 'Eye-tracking (отслеживание движения зрачков глаз)', 'required' => false, 'type' => 'checkbox'],
				['name' => 'mouse', 'title' => 'Mouse-tracking (отслеживание движения курсора мыши)', 'required' => false, 'type' => 'checkbox'],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid],
				['name' => 'step-mechanics', 'type' => 'hidden', 'value' => true]
			];
        } else {
            $fields = [
				['name' => 'set_id', 'title' => 'Набор вопросов', 'required' => false, 'type' => 'select', 'options' => $sets, 'value' => $heap['set_id']],
				['title' => 'Дополнительные механики нейротеста', 'type' => 'heading'],
				['name' => 'eye', 'title' => 'Eye-tracking (отслеживание движения зрачков глаз)', 'required' => false, 'type' => 'checkbox', 'value' => $options & \App\Models\TestOptions::EYE_TRACKING->value],
				['name' => 'mouse', 'title' => 'Mouse-tracking (отслеживание движения курсора мыши)', 'required' => false, 'type' => 'checkbox', 'value' => $options & \App\Models\TestOptions::MOUSE_TRACKING->value],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid],
				['name' => 'step-mechanics', 'type' => 'hidden', 'value' => true]
			];
        }
	@endphp
@endsection

