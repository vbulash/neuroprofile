@extends('layouts.detail')

@section('header')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Аккаунт менеджеры', 'active' => true, 'context' => 'admin', 'link' => route('adminclients.index')]];
	@endphp
@endsection

@section('interior.header')
	Просмотр анкеты аккаунт менеджера &laquo;{{ $admin->name }}&raquo;
@endsection

@section('form.params')
	id="admin-edit" name="admin-edit"
	action=""
@endsection

@section('form.fields')
	@php
		$fields = [
		    [
		        'name' => 'name',
		        'title' => 'Фамилия, имя и отчество',
		        'required' => true,
		        'type' => 'text',
		        'value' => $admin->name,
		    ],
		    [
		        'name' => 'email',
		        'title' => 'Электронная почта',
		        'required' => false,
		        'type' => 'email',
		        'value' => $admin->email,
		    ],
		    [
		        'name' => '_clients',
		        'title' => 'Управляет клиентами',
		        'required' => false,
		        'type' => 'select',
		        'value' => $clients,
		        'options' => $allclients,
		        'multiple' => true,
		    ],
		];
	@endphp
@endsection

@section('form.close')
	{{ route('adminclients.index') }}
@endsection
