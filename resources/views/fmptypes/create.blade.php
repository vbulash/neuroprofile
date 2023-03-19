@extends('layouts.detail')

@section('service')
	Работа с описаниями результатов тестирования
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Тип описания', 'active' => true, 'context' => 'fmptype', 'link' => route('fmptypes.index')], ['title' => 'Нейропрофили', 'active' => false, 'context' => 'profile'], ['title' => 'Блоки описания', 'active' => false, 'context' => 'block']];
	@endphp
@endsection

@section('interior.header')
	Новый тип описания
@endsection

@section('form.params')
	id="{{ form(\App\Models\FMPType::class, $mode, 'id') }}" name="{{ form(\App\Models\FMPType::class, $mode, 'name') }}"
	action="{{ form(\App\Models\FMPType::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
		    ['name' => 'name', 'title' => 'Наименование', 'required' => true, 'type' => 'text'],
		    [
		        'name' => 'cluster',
		        'title' => 'Тип',
		        'required' => true,
		        'type' => 'radio',
		        'options' => [
		            0 => 'ФМП',
		            1 => 'Нейропрофиль',
		        ],
		    ],
		    ['name' => 'active', 'title' => 'Статус типа описания', 'required' => false, 'type' => 'text', 'value' => 'Неактивный', 'disabled' => true],
		    ['name' => 'limit', 'title' => 'Необходимо нейропрофилей', 'required' => true, 'type' => 'number', 'value' => 16, 'min' => 2, 'max' => $max],
		    ['name' => 'ethalon', 'title' => 'Эталонный тип описания', 'required' => false, 'type' => 'checkbox'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\FMPType::class, $mode, 'close') }}
@endsection
