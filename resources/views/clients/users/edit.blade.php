@extends('layouts.detail')

@section('service')
	Работа с клиентами и контрактами
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Клиент',
		        'active' => false,
		        'context' => 'client',
		        'link' => route('clients.index'),
		    ],
		    [
		        'title' => 'Аккаунт менеджер',
		        'active' => true,
		        'context' => '',
		        'link' => route('clients.users.index', ['client' => $client->getKey()]),
		    ],
		];
	@endphp
@endsection

@section('interior.header')
	Редактирование анкеты аккаунт менеджера &laquo;{{ $admin->name }}&raquo;
@endsection

@section('form.params')
	id="client-admin-edit" name="client-admin-edit"
	action="{{ route('clients.users.update', ['client' => $client->getKey(), 'user' => $admin->getKey()]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text', 'value' => $admin->name], ['name' => 'email', 'title' => 'Электронная почта', 'required' => false, 'type' => 'email', 'value' => $admin->email, 'disabled' => true], ['name' => 'password', 'title' => 'Новый пароль', 'required' => false, 'type' => 'password', 'generate' => true], ['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => false, 'type' => 'password']];
	@endphp
@endsection

@section('form.close')
	{{ route('clients.users.index', ['client' => $client->getKey()]) }}
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
