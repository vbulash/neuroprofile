@extends('layouts.detail')

@section('service')@endsection

@section('steps')
	@php
	$steps = [
            ['title' => 'Тип вопросов тестов', 'active' => true, 'context' => 'kind', 'link' => route('kinds.index') ],
        ];
	@endphp
@endsection

@section('interior.header')
    Новый тип вопросов тестов
@endsection

@section('form.params')
    id="{{ form(\App\Models\Kind::class, $mode, 'id') }}" name="{{ form(\App\Models\Kind::class, $mode, 'name') }}"
    action="{{ form(\App\Models\Kind::class, $mode, 'action') }}"
@endsection

@section('form.fields')
    @php
        $fields = [
            ['name' => 'name', 'title' => 'Название', 'required' => true, 'type' => 'text'],
			['name' => 'cue', 'title' => 'Подсказка к вопросам', 'required' => true, 'type' => 'text'],
			['name' => 'images', 'title' => 'Количество изображений вопроса', 'required' => true, 'type' => 'number', 'value' => 2, 'min' => 2],
			['name' => 'answers', 'title' => 'Количество ответов вопроса', 'required' => true, 'type' => 'number', 'value' => 1, 'min' => 1],
			['type' => 'heading', 'title' => 'На одной строке - только один ключ. Для варианта множественного ответа символ &laquo;*&raquo; отвечает за маску'],
			['name' => 'keys', 'title' => 'Набор ключей вопросов', 'required' => true, 'type' => 'textarea'],
        ];
    @endphp
@endsection

@section('form.close')
    {{ form(\App\Models\Kind::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('keys').setAttribute('rows', '14');
		}, false);
	</script>
@endpush
