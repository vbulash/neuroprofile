@extends('layouts.detail')

@section('service')
	Работа с клиентами и контрактами
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Клиент', 'active' => true, 'context' => 'client', 'link' => route('clients.index')], ['title' => 'Администраторы клиента<br/>Контракты', 'active' => false, 'context' => 'contract'], ['title' => 'Информация о контракте', 'active' => false, 'context' => 'info']];
	@endphp
@endsection

@section('interior.header')
	Новый клиент
@endsection

@section('form.params')
	id="{{ form(\App\Models\Client::class, $mode, 'id') }}" name="{{ form(\App\Models\Client::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Client::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Наименование клиента', 'required' => true, 'type' => 'text'], ['name' => 'inn', 'title' => 'ИНН клиента', 'required' => true, 'type' => 'text'], ['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП клиента', 'required' => true, 'type' => 'text'], ['name' => 'address', 'title' => 'Адрес', 'required' => true, 'type' => 'textarea'], ['name' => 'phone', 'title' => 'Телефон', 'required' => false, 'type' => 'text'], ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email']];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Client::class, $mode, 'close') }}
@endsection
