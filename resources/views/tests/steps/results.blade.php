@extends('tests.steps.wizard')

@section('service')Создание теста@endsection

@section('interior.subheader') @endsection

@section('form.fields')
	@php
        $heap = session('heap');
        $options = intval($heap['options']);
        if (isset($heap) && isset($heap['step-results'])) {
            $fields = [
                ['title' => 'Показ результата тестирования на экране', 'type' => 'heading'],
				['name' => 'show-option', 'title' => 'Показать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['show'])],
				['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['show'] ?? 0],
				['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'],
				['name' => 'mail-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['mail'])],
				['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['mail'] ?? 0],
				['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'],
				['name' => 'client-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox', 'value' => isset($heap['descriptions']['client'])],
				['name' => 'client', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes, 'value' => $heap['descriptions']['client'] ?? 0],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid]
			];
        } else {
			$fields = [
                ['title' => 'Показ результата тестирования на экране', 'type' => 'heading'],
				['name' => 'show-option', 'title' => 'Показать результат тестирования', 'required' => false, 'type' => 'checkbox'],
				['name' => 'show', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes],
				['title' => 'Письмо респонденту с результатом тестирования', 'type' => 'heading'],
				['name' => 'mail-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox'],
				['name' => 'mail', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes],
				['title' => 'Письмо клиенту с результатом тестирования', 'type' => 'heading'],
				['name' => 'client-option', 'title' => 'Переслать результат тестирования', 'required' => false, 'type' => 'checkbox'],
				['name' => 'client', 'title' => 'Будет использован тип описания', 'required' => false, 'type' => 'select', 'options' => $fmptypes],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid]
			];
        }
	@endphp
@endsection

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
