@extends('layouts.detail')

@section('service')
	Работа с описаниями результатов тестирования
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Тип описания',
		        'active' => true,
		        'context' => 'fmptype',
		        'link' => route('fmptypes.index'),
		    ],
		    ['title' => 'Нейропрофили', 'active' => false, 'context' => 'profile'],
		    ['title' => 'Блоки описания', 'active' => false, 'context' => 'block'],
		];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif типа описания &laquo;{{ $fmptype->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($fmptype, $mode, 'id') }}" name="{{ form($fmptype, $mode, 'name') }}"
	action="{{ form($fmptype, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
		    ['name' => 'name', 'title' => 'Наименование', 'required' => true, 'type' => 'text', 'value' => $fmptype->name],
		    [
		        'name' => 'cluster',
		        'title' => 'Тип',
		        'required' => true,
		        'type' => 'radio',
		        'options' => [
		            0 => 'ФМП',
		            1 => 'Нейропрофиль',
		        ],
		        'value' => $fmptype->cluster,
		    ],
		    ['name' => 'active', 'title' => 'Статус типа описания', 'required' => false, 'type' => 'text', 'value' => $fmptype->active ? 'Активный' : 'Неактивный', 'disabled' => true],
		    ['name' => 'limit', 'title' => 'Необходимо нейропрофилей', 'required' => true, 'type' => 'number', 'value' => $fmptype->limit, 'min' => 2, 'max' => $max],
		    ['name' => 'ethalon', 'title' => 'Эталонный тип описания', 'required' => false, 'type' => 'checkbox', 'value' => $fmptype->ethalon],
		    ['name' => 'id', 'type' => 'hidden', 'value' => $fmptype->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($fmptype, $mode, 'close') }}
@endsection
