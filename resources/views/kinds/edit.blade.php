@extends('layouts.detail')

@section('service')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Тип вопросов тестов', 'active' => true, 'context' => 'kind', 'link' => route('kinds.index')]];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif типа вопросов теста &laquo;{{ $kind->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($kind, $mode, 'id') }}" name="{{ form($kind, $mode, 'name') }}"
	action="{{ form($kind, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'Название', 'required' => true, 'type' => 'text', 'value' => $kind->name];
		$fields[] = ['name' => 'images', 'title' => 'Количество изображений вопроса', 'required' => true, 'type' => 'number', 'value' => $kind->images, 'min' => 2];
		$fields[] = ['name' => 'answers', 'title' => 'Количество ответов вопроса', 'required' => true, 'type' => 'number', 'value' => $kind->answers, 'min' => 1];
		if ($mode == config('global.edit')) {
		    $fields[] = ['type' => 'heading', 'title' => 'На одной строке - только один ключ. Для варианта множественного ответа символ &laquo;*&raquo; отвечает за маску'];
		}
		$fields[] = ['name' => 'keys', 'title' => 'Множество ключей вопросов', 'required' => true, 'type' => 'textarea', 'value' => $kind->keys];
	@endphp
@endsection

@section('form.close')
	{{ form($kind, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('keys').setAttribute('rows', '14');
		}, false);
	</script>
@endpush
