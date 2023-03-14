@extends('layouts.detail')

@section('header')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Администраторы платформы', 'active' => true, 'context' => 'admin', 'link' => route('admins.index')]];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif анкеты администратора &laquo;{{ $admin->name }}&raquo;
@endsection

@section('form.params')
	id="admin-edit" name="admin-edit"
	action="{{ route('admins.update', ['admin' => $admin->getKey()]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text', 'value' => $admin->name], ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $admin->email], ['name' => 'password', 'title' => 'Новый пароль', 'required' => false, 'type' => 'password', 'generate' => true], ['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => false, 'type' => 'password']];
	@endphp
@endsection

@section('form.close')
	{{ route('admins.index') }}
@endsection

@push('js_after')
	<script>
		$(function() {
			$("#get-password").on("click", (event) => {
				event.preventDefault();
				$.post({
					url: "{{ route('api.get.password', ['length' => 10]) }}",
					datatype: "json",
					success: (helper) => {
						$("#password").val(helper.password);
					}
				});
			});
		});
	</script>
@endpush
