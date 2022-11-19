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

@section('interior.subheader')
@endsection

@section('form.fields')
	@php
		$heap = session('heap');
		$options = intval($heap['options']);

		$fields = [];
		if (!isset($heap['step-results']) && $mode == config('global.create')) {
		    if (env('RESEARCH')) {
		        $fields[] = ['title' => "В режиме исследовательской платформы результаты тестирования не отображаются / не пересылаются. Все поля ниже заблокированы и не могут быть изменены или использованы далее", 'type' => 'heading'];
		        $fields[] = ['title' => 'Показ результата тестирования на экране', 'type' => 'heading'];
		        $fields[] = ['name' => 'show-option', 'title' => 'Показать результат тестирования', 'required' => false, 'type' => 'checkbox', 'disabled' => true];
		        $fields[] = ['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'disabled' => true];
		        $fields[] = ['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'mail-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'disabled' => true];
		        $fields[] = ['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'disabled' => true];
		        $fields[] = ['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'client-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'disabled' => true];
		        $fields[] = ['name' => 'client', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'disabled' => true];
		    } else {
		        $fields[] = ['title' => 'Показ результата тестирования на экране', 'type' => 'heading'];
		        $fields[] = ['name' => 'show-option', 'title' => 'Показать результат тестирования', 'required' => false, 'type' => 'checkbox'];
		        $fields[] = ['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes];
		        $fields[] = ['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'mail-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox'];
		        $fields[] = ['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes];
		        $fields[] = ['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'client-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox'];
		        $fields[] = ['name' => 'client', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes];
		    }
		    $fields[] = ['name' => 'mode', 'type' => 'hidden', 'value' => $mode];
		    $fields[] = ['name' => 'test', 'type' => 'hidden', 'value' => $test];
		    $fields[] = ['name' => 'step-results', 'type' => 'hidden', 'value' => true];
		} else {
		    $fields[] = ['title' => 'Показ результата тестирования на экране', 'type' => 'heading'];
		    if (env('RESEARCH')) {
		        $fields[] = ['title' => "В режиме исследовательской платформы результаты тестирования не отображаются / не пересылаются. Все поля ниже заблокированы и не могут быть изменены или использованы далее", 'type' => 'heading'];
		        $fields[] = ['name' => 'show-option', 'title' => 'Показать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['show']), 'disabled' => true];
		        $fields[] = ['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['show'] ?? 0, 'disabled' => true];
		        $fields[] = ['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'mail-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['mail']), 'disabled' => true];
		        $fields[] = ['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['mail'] ?? 0, 'disabled' => true];
		        $fields[] = ['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'client-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['client']), 'disabled' => true];
		        $fields[] = ['name' => 'client', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['client'] ?? 0, 'disabled' => true];
		    } else {
		        $fields[] = ['name' => 'show-option', 'title' => 'Показать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['show'])];
		        $fields[] = ['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['show'] ?? 0];
		        $fields[] = ['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'mail-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['mail'])];
		        $fields[] = ['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['mail'] ?? 0];
		        $fields[] = ['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'client-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['client'])];
		        $fields[] = ['name' => 'client', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['client'] ?? 0];
		    }
		    $fields[] = ['name' => 'mode', 'type' => 'hidden', 'value' => $mode];
		    $fields[] = ['name' => 'test', 'type' => 'hidden', 'value' => $test];
		    $fields[] = ['name' => 'step-results', 'type' => 'hidden', 'value' => true];
		}
	@endphp
@endsection

@if ($mode != config('global.show'))
	@push('js_after')
		<script>
			document.getElementById('show-option').addEventListener('change', (event) => {
				document.getElementById('show').disabled = !event.target.checked;
			});
			document.getElementById('mail-option').addEventListener('change', (event) => {
				document.getElementById('mail').disabled = !event.target.checked;
			});
			document.getElementById('client-option').addEventListener('change', (event) => {
				document.getElementById('client').disabled = !event.target.checked;
			});

			document.addEventListener("DOMContentLoaded", () => {
				document.getElementById('show').disabled = !document.getElementById('show-option').checked;
				document.getElementById('mail').disabled = !document.getElementById('mail-option').checked;
				document.getElementById('client').disabled = !document.getElementById('client-option').checked;
			}, false);
		</script>
	@endpush
@endif
