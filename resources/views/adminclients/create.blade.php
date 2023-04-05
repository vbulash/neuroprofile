@extends('layouts.detail')

@section('header')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Аккаунт менеджеры', 'active' => true, 'context' => 'admin', 'link' => route('admins.index')]];
	@endphp
@endsection

@section('interior.header')
	Новый аккаунт менеджер
@endsection

@section('form.params')
	id="adminclient-create"
	name="adminclient-create"
	action="{{ route('adminclients.store') }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text'], ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'], ['name' => 'password', 'title' => 'Новый пароль', 'required' => true, 'type' => 'password', 'generate' => true], ['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => true, 'type' => 'password']];
	@endphp
@endsection

@section('form.close')
	{{ route('adminclients.index') }}
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
