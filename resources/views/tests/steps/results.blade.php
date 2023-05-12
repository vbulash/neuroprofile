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
		$heap = session('heap');
		$options = intval($heap['options']);

		$fields = [];
		if (!isset($heap['step-results']) && $mode == config('global.create')) {
		    if (env('EXEC_MODE') == 'research') {
		        $fields[] = ['title' => 'В режиме исследовательской платформы результаты тестирования не отображаются / не пересылаются. Все поля ниже заблокированы и не могут быть изменены или использованы далее', 'type' => 'heading'];
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
		        $fields[] = ['name' => 'show-name-option', 'title' => '<strong>Показывать</strong> названия блоков в результатах тестирования', 'required' => false, 'type' => 'checkbox', 'value' => 'on'];
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
		    if (env('EXEC_MODE') == 'research') {
		        $fields[] = ['title' => 'В режиме исследовательской платформы результаты тестирования не отображаются / не пересылаются. Все поля ниже заблокированы и не могут быть изменены или использованы далее', 'type' => 'heading'];
		        $fields[] = ['title' => 'Показ результата тестирования на экране', 'type' => 'heading'];
		        $fields[] = ['name' => 'show-option', 'title' => 'Показать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['show']), 'disabled' => true];
		        $fields[] = ['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['show'] ?? 0, 'disabled' => true];
		        $fields[] = ['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'mail-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['mail']), 'disabled' => true];
		        $fields[] = ['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['mail'] ?? 0, 'disabled' => true];
		        $fields[] = ['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = ['name' => 'client-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['client']), 'disabled' => true];
		        $fields[] = ['name' => 'client', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['client'] ?? 0, 'disabled' => true];
		    } else {
		        $dont_show_title = $options & \App\Models\TestOptions::DONT_SHOW_TITLE->value;
		        $fields[] = [
		            'name' => 'show-name-option',
		            'title' => $dont_show_title ? '<strong>Не показывать</strong> названия блоков в результатах тестирования' : 'Показ названия блока в результатах тестирования зависит от <strong>индивидуальной настройки блока</strong>',
		            'required' => false,
		            'type' => 'checkbox',
		            'value' => $dont_show_title ? '' : 'on',
		        ];
		        $fields[] = ['title' => 'Показ результата тестирования на экране', 'type' => 'heading'];
		        $fields[] = [
		            'name' => 'show-option',
		            'title' => isset($heap['descriptions']['show']) ? '<strong>Показать</strong> результат тестирования на экране' : '<strong>Не показывать</strong> результат тестирования на экране',
		            'required' => false,
		            'type' => 'checkbox',
		            'value' => isset($heap['descriptions']['show']),
		        ];
		        $fields[] = ['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['show'] ?? 0];
		        $fields[] = ['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = [
		            'name' => 'mail-option',
		            'title' => isset($heap['descriptions']['mail']) ? '<strong>Переслать</strong> результат тестирования респонденту' : '<strong>Не пересылать</strong> результат тестирования респонденту',
		            'required' => false,
		            'type' => 'checkbox',
		            'value' => isset($heap['descriptions']['mail']),
		        ];
		        $fields[] = ['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['mail'] ?? 0];
		        $fields[] = ['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'];
		        $fields[] = [
		            'name' => 'client-option',
		            'title' => isset($heap['descriptions']['client']) ? '<strong>Переслать</strong> результат тестирования клиенту' : '<strong>Не пересылать</strong> результат тестирования клиенту',
		            'required' => false,
		            'type' => 'checkbox',
		            'value' => isset($heap['descriptions']['client']),
		        ];
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
				document.getElementById('show-option-label').innerHTML =
					(event.target.checked ?
						"<strong>Показать</strong> результат тестирования на экране" :
						"<strong>Не показывать</strong> результат тестирования на экране");
			});
			document.getElementById('mail-option').addEventListener('change', (event) => {
				document.getElementById('mail').disabled = !event.target.checked;
				document.getElementById('mail-option-label').innerHTML =
					(event.target.checked ?
						"<strong>Переслать</strong> результат тестирования респонденту" :
						"<strong>Не пересылать</strong> результат тестирования респонденту");
			});
			document.getElementById('client-option').addEventListener('change', (event) => {
				document.getElementById('client').disabled = !event.target.checked;
				document.getElementById('client-option-label').innerHTML =
					(event.target.checked ?
						"<strong>Переслать</strong> результат тестирования клиенту" :
						"<strong>Не пересылать</strong> результат тестирования клиенту");
			});
			document.getElementById('show-name-option').addEventListener('change', (event) => {
				document.getElementById('show-name-option-label').innerHTML =
					(event.target.checked ?
						"Показ названия блока в результатах тестирования зависит от <strong>индивидуальной настройки блока</strong>" :
						"<strong>Не показывать</strong> названия блоков в результатах тестирования");
			});

			document.addEventListener("DOMContentLoaded", () => {
				document.getElementById('show').disabled = !document.getElementById('show-option').checked;
				document.getElementById('mail').disabled = !document.getElementById('mail-option').checked;
				document.getElementById('client').disabled = !document.getElementById('client-option').checked;
				document.getElementById('show-name-option').dispatchEvent(new Event('change'));
			}, false);
		</script>
	@endpush
@endif
