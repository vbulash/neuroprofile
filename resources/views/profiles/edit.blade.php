@extends('layouts.detail')

@section('service')
	Работа с клиентами и контрактами
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Тип описания',
		        'active' => false,
		        'context' => 'fmptype',
		        'link' => route('fmptypes.index'),
		    ],
		    ['title' => 'Нейропрофили', 'active' => true, 'context' => 'profile', 'link' => route('profiles.index')],
		    ['title' => 'Блоки описания', 'active' => false, 'context' => 'block'],
		];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif нейропрофиля &laquo;{{ $profile->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($profile, $mode, 'id') }}" name="{{ form($profile, $mode, 'name') }}"
	action="{{ form($profile, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'code', 'title' => 'Код нейропрофиля', 'required' => false, 'type' => 'select', 'options' => $codes, 'value' => $profile->code, 'disabled' => true], ['name' => 'name', 'title' => 'Название нейропрофиля', 'required' => true, 'type' => 'text', 'value' => $profile->name], ['name' => 'fmptype_id', 'type' => 'hidden', 'value' => $profile->fmptype->getKey()]];
	@endphp
@endsection

@section('form.close')
	{{ form($profile, $mode, 'close') }}
@endsection
