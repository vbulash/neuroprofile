@extends('layouts.detail')

@section('service')Работа с клиентами и контрактами@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index', ['sid' => session()->getId()])],
			['title' => 'Нейропрофиль', 'active' => true, 'context' => 'profile', 'link' => route('profiles.index', ['sid' => session()->getId()])],
			['title' => 'Блок описания', 'active' => false, 'context' => 'block'],
		];
	@endphp
@endsection

@section('interior.header')
	Новый нейропрофиль
@endsection

@section('form.params')
	id="{{ form(\App\Models\Profile::class, $mode, 'id') }}" name="{{ form(\App\Models\Profile::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Profile::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'code', 'title' => 'Код нейропрофиля', 'required' => true, 'type' => 'select', 'options' => $codes],
			['name' => 'name', 'title' => 'Название нейропрофиля', 'required' => true, 'type' => 'text'],
			['name' => 'fmptype_id', 'type' => 'hidden', 'value' => $fmptype->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Profile::class, $mode, 'close') }}
@endsection
