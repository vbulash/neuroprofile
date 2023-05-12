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
		@endif теста &laquo;{{ $heap['name'] }}&raquo; ({{ $heap['client'] }})
	@endif
@endsection

@section('form.fields')
	@php
		$heap = session('heap');
		$auth = intval($heap['options'] ?? 0);

		$fields = [['name' => 'mode', 'type' => 'hidden', 'value' => $mode], ['name' => 'test', 'type' => 'hidden', 'value' => $test], ['name' => 'step-card', 'type' => 'hidden', 'value' => true]];
		if ($auth & \App\Models\TestOptions::AUTH_FULL->value || $auth & \App\Models\TestOptions::AUTH_MIX->value) {
		    foreach (\App\Models\Test::$fields as $group) {
		        foreach ($group as $control) {
		            $item = [
		                'name' => $control['name'],
		                'title' => $control['label'],
		                'required' => 'false',
		                'type' => 'checkbox',
		                'short' => true,
		            ];
		            if (isset($heap['card'])) {
		                $item['value'] = array_key_exists($control['name'], $heap['card']);
		            } else {
		                $item['value'] = false; //$control['actual'] | $control['required'];
		            }
		            $fields[] = $item;
		        }
		        $fields[] = ['title' => ' ', 'type' => 'heading'];
		    }
		}
	@endphp
@endsection

@if ($auth & \App\Models\TestOptions::AUTH_FULL->value || $auth & \App\Models\TestOptions::AUTH_MIX->value)
	@section('interior.header')
		Выбор отображаемых полей анкеты теста
	@endsection
@endif
@section('interior.subheader')
@endsection

@section('form.content')
	@if ($auth & \App\Models\TestOptions::AUTH_GUEST->value)
		<div class="form-group mb-4">
			Сбор сведений о респондентах не требуется.<br />
			Перейдите к следующему шагу настройки нового теста
		</div>
	@elseif ($auth & \App\Models\TestOptions::AUTH_PKEY->value)
		<div class="form-group mb-4" id="auth-pkey">
			Сбор сведений о респондентах не требуется.<br />
			В начале начале тестирования будет запрашиваться персональный ключ (pkey).<br />
			Перейдите к следующему шагу настройки нового теста
		</div>
	@endif
@endsection
