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
		        'title' => 'Аккаунт менеджеры',
		        'active' => true,
		        'context' => '',
		    ],
		];
	@endphp
@endsection

@section('interior.header')
	Новый аккаунт менеджер
@endsection

@section('form.params')
	id="clientadmin-create"
	name="clientadmin-create"
	action="{{ route('clients.users.store', ['client' => $client->getKey()]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text'], ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'], ['name' => 'password', 'title' => 'Новый пароль', 'required' => true, 'type' => 'password', 'generate' => true], ['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => true, 'type' => 'password']];
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
