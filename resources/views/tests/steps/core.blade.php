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

@section('form.fields')
	@php
		$auth = [
            \App\Models\TestOptions::AUTH_GUEST->value => 'Нет анкеты, только запрос разрешений',
            \App\Models\TestOptions::AUTH_FULL->value => 'Полная анкета, максимум информации о респонденте',
            \App\Models\TestOptions::AUTH_PKEY->value => 'Анкета не применима, запрашивается персональный ключ лицензии',
            \App\Models\TestOptions::AUTH_MIX->value => 'Комбинация запросов анкеты респондента и персонального ключа'
		];
        $heap = session('heap');

        if (!isset($heap['step-core']) && $mode == config('global.create')) {
            $fields = [
				['name' => 'name', 'title' => 'Название теста', 'required' => true, 'type' => 'text'],
				['name' => 'contract_id', 'title' => 'Контракт теста', 'required' => false, 'type' => 'select', 'options' => $contracts],
				['name' => 'auth', 'title' => 'Анкетирование в начале теста', 'required' => false, 'type' => 'select', 'options' => $auth],
				['name' => 'paid', 'title' => 'Результат тестирования имеет платную расширенную версию', 'required' => false, 'type' => 'checkbox'],
				['name' => 'options', 'type' => 'hidden', 'value' => 0],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid],
				['name' => 'step-core', 'type' => 'hidden', 'value' => true]
			];
        } else {
            $auth_current = 0;
            foreach (\App\Models\TestOptions::cases() as $case) {
                if (intval($heap['options']) & $case->value) {
                    $auth_current = $case->value;
                    break;
                }
            }
            $fields = [
				['name' => 'name', 'title' => 'Название теста', 'required' => true, 'type' => 'text', 'value' => $heap['name']],
				['name' => 'contract_id', 'title' => 'Контракт теста', 'required' => false, 'type' => 'select', 'options' => $contracts, 'value' => $heap['contract_id']],
				['name' => 'auth', 'title' => 'Анкетирование в начале теста', 'required' => false, 'type' => 'select', 'options' => $auth, 'value' => $auth_current],
				['name' => 'paid', 'title' => 'Результат тестирования имеет платную расширенную версию', 'required' => false, 'type' => 'checkbox', 'value' => $heap['paid']],
				['name' => 'options', 'type' => 'hidden', 'value' => $heap['options']],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid],
				['name' => 'step-core', 'type' => 'hidden', 'value' => true]
			];
            if ($mode != config('global.create')) {
                array_unshift($fields,
                	['name' => 'tkey', 'title' => 'Ключ теста', 'required' => false, 'type' => 'text', 'value' => $heap['key'], 'disabled' => true]);
                $fields[] = ['name' => 'key', 'type' => 'hidden', 'value' => $heap['key']];
            }
        }
	@endphp
@endsection

