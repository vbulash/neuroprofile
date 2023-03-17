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
	id="{{ form(\App\Models\ClientAdmin::class, $mode, 'id') }}"
	name="{{ form(\App\Models\ClientAdmin::class, $mode, 'name') }}"
	action="{{ form(\App\Models\ClientAdmin::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text'], ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'], ['name' => 'password', 'title' => 'Новый пароль', 'required' => true, 'type' => 'password', 'generate' => true], ['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => true, 'type' => 'password']];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\ClientAdmin::class, $mode, 'close') }}
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
