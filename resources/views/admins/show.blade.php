@extends('layouts.detail')

@section('header')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Администраторы платформы', 'active' => true, 'context' => 'admin', 'link' => route('admins.index')]];
	@endphp
@endsection

@section('interior.header')
	Просмотр анкеты администратора &laquo;{{ $admin->name }}&raquo;
@endsection

@section('form.params')
	id="admin-edit" name="admin-edit"
	action="{{ route('admins.update', ['admin' => $admin->getKey()]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text', 'value' => $admin->name], ['name' => 'email', 'title' => 'Электронная почта', 'required' => false, 'type' => 'email', 'value' => $admin->email]];
	@endphp
@endsection

@section('form.close')
	{{ route('admins.index') }}
@endsection
