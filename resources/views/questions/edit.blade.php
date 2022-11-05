@extends('layouts.detail')

@section('service')
	Работа с вопросами тестирования
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Набор вопросов', 'active' => false, 'context' => 'set', 'link' => route('sets.index')], ['title' => 'Вопрос', 'active' => true, 'context' => 'question', 'link' => route('questions.index')], ['title' => 'Изображения вопросов', 'active' => false, 'context' => 'part']];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif вопроса № {{ $question->sort_no }} для набора вопросов
	&laquo;{{ $question->set->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($question, $mode, 'id') }}" name="{{ form($question, $mode, 'name') }}"
	action="{{ form($question, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'kindname', 'title' => 'Тип вопроса', 'required' => false, 'type' => 'text', 'value' => $question->kind->name, 'disabled' => true];
		$fields[] = [
		    'name' => 'learning',
		    'title' => 'Режим прохождения',
		    'required' => true,
		    'type' => 'select',
		    'options' => [
		        '0' => 'Реальный вопрос',
		        '1' => 'Учебный вопрос',
		    ],
		    'value' => $question->learning,
		];
		$fields[] = ['name' => 'timeout', 'title' => 'Таймаут прохождения вопроса, секунд', 'required' => true, 'type' => 'number', 'value' => $question->timeout, 'min' => 0];
		$fields[] = ['name' => 'cue', 'title' => 'Отдельная подсказка к вопросу', 'required' => false, 'type' => 'text', 'value' => $question->cue ?? ''];
	@endphp
@endsection

@section('form.close')
	{{ form($question, $mode, 'close') }}
@endsection
