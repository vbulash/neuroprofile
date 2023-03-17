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
	id="{{ form(\App\Models\Admin::class, $mode, 'id') }}" name="{{ form(\App\Models\Admin::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Admin::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text'], ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'], ['name' => 'password', 'title' => 'Новый пароль', 'required' => true, 'type' => 'password', 'generate' => true], ['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => true, 'type' => 'password']];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Admin::class, $mode, 'close') }}
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
