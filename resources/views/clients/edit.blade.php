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
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif анкеты клиента
@endsection

@section('form.params')
	id="{{ form($client, $mode, 'id') }}" name="{{ form($client, $mode, 'name') }}"
	action="{{ form($client, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Наименование клиента', 'required' => true, 'type' => 'text', 'value' => $client->name], ['name' => 'inn', 'title' => 'ИНН клиента', 'required' => true, 'type' => 'text', 'value' => $client->inn], ['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП клиента', 'required' => true, 'type' => 'text', 'value' => $client->ogrn], ['name' => 'address', 'title' => 'Адрес', 'required' => true, 'type' => 'textarea', 'value' => $client->address], ['name' => 'phone', 'title' => 'Телефон', 'required' => false, 'type' => 'text', 'value' => $client->phone], ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $client->email]];
	@endphp
@endsection

@section('form.close')
	{{ form($client, $mode, 'close') }}
@endsection
