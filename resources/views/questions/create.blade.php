@extends('layouts.detail')

@section('service')
	Работа с вопросами тестирования
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Набор вопросов', 'active' => false, 'context' => 'set', 'link' => route('sets.index')], ['title' => 'Вопросы', 'active' => true, 'context' => 'question', 'link' => route('questions.index')], ['title' => 'Изображения вопросов', 'active' => false, 'context' => 'part']];
	@endphp
@endsection

@section('interior.header')
	Новый вопрос
@endsection

@section('form.params')
	id="{{ form(\App\Models\Question::class, $mode, 'id') }}" name="{{ form(\App\Models\Question::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Question::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'kind', 'type' => 'hidden', 'value' => $kind->getKey()];
		$fields[] = ['name' => 'kindname', 'title' => 'Тип вопроса', 'required' => false, 'type' => 'text', 'value' => $kind->name, 'disabled' => true];
		$fields[] = [
		    'name' => 'learning',
		    'title' => 'Режим прохождения',
		    'required' => true,
		    'type' => 'select',
		    'options' => [
		        '0' => 'Реальный вопрос',
		        '1' => 'Учебный вопрос',
		    ],
		];
		$fields[] = ['name' => 'timeout', 'title' => 'Таймаут прохождения вопроса, секунд', 'required' => true, 'type' => 'number', 'value' => 0, 'min' => 0];
		$fields[] = ['name' => 'cue', 'title' => 'Отдельная подсказка к вопросу', 'required' => false, 'type' => 'text'];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Question::class, $mode, 'close') }}
@endsection
