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

@section('interior.subheader')
@endsection

@section('form.fields')
	@php
		$auth = [
		    \App\Models\TestOptions::AUTH_GUEST->value => 'Нет анкеты, только запрос разрешений',
		    \App\Models\TestOptions::AUTH_FULL->value => 'Полная анкета, максимум информации о респонденте',
		    \App\Models\TestOptions::AUTH_PKEY->value => 'Анкета не применима, запрашивается персональный ключ лицензии',
		    \App\Models\TestOptions::AUTH_MIX->value => 'Комбинация запросов анкеты респондента и персонального ключа',
		];
		$heap = session('heap');
		
		if (!isset($heap['step-core']) && $mode == config('global.create')) {
		    $fields = [
		        ['name' => 'name', 'title' => 'Название теста', 'required' => false, 'type' => 'text'],
		        ['name' => 'contract_id', 'title' => 'Контракт теста', 'required' => false, 'type' => 'select', 'options' => $contracts],
		        ['name' => 'auth', 'title' => 'Анкетирование в начале теста', 'required' => false, 'type' => 'select', 'options' => $auth],
		        [
		            'name' => 'paid',
		            'title' => 'Результат тестирования <strong>не имеет платной</strong> расширенной версии',
		            'required' => false,
		            'type' => 'checkbox',
		        ],
		        ['name' => 'options', 'type' => 'hidden', 'value' => 0],
		        ['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
		        ['name' => 'test', 'type' => 'hidden', 'value' => $test],
		        ['name' => 'step-core', 'type' => 'hidden', 'value' => true],
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
		        ['name' => 'name', 'title' => 'Название теста', 'required' => false, 'type' => 'text', 'value' => $heap['name']],
		        ['name' => 'contract_id', 'title' => 'Контракт теста', 'required' => false, 'type' => 'select', 'options' => $contracts, 'value' => $heap['contract_id']],
		        ['name' => 'auth', 'title' => 'Анкетирование в начале теста', 'required' => false, 'type' => 'select', 'options' => $auth, 'value' => $auth_current],
		        [
		            'name' => 'paid',
		            'title' => 'Результат тестирования <strong>не имеет платной</strong> расширенной версии',
		            'required' => false,
		            'type' => 'checkbox',
		            'value' => $heap['paid'],
		        ],
		        ['name' => 'options', 'type' => 'hidden', 'value' => $heap['options']],
		        ['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
		        ['name' => 'test', 'type' => 'hidden', 'value' => $test],
		        ['name' => 'step-core', 'type' => 'hidden', 'value' => true],
		    ];
		    if ($mode != config('global.create')) {
		        array_unshift($fields, ['name' => 'tkey', 'title' => 'Ключ теста', 'required' => false, 'type' => 'text', 'value' => $heap['key'], 'disabled' => true]);
		        $fields[] = ['name' => 'key', 'type' => 'hidden', 'value' => $heap['key']];
		    }
		}
	@endphp
@endsection

@push('js_after')
	<script>
		document.getElementById('paid').addEventListener('change', (event) => {
			document.getElementById('paid-label').innerHTML =
				(event.target.checked ?
					"Результат тестирования <strong>имеет платную</strong> расширенную версию" :
					"Результат тестирования <strong>не имеет платной</strong> расширенной версии");
		}, false);

		document.getElementById('core-create').addEventListener('submit', (event) => {
			if (document.getElementById('name').value.length <= 1)
				document.getElementById('name').value = ".";
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('paid').dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
