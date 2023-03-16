@extends('layouts.detail')

@section('header')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Администраторы клиентов', 'active' => true, 'context' => 'admin', 'link' => route('admins.index')]];
	@endphp
@endsection

@section('interior.header')
	Редактирование анкеты администратора клиентов &laquo;{{ $admin->name }}&raquo;
@endsection

@section('form.params')
	id="admin-edit" name="admin-edit"
	action="{{ route('adminclients.update', ['adminclient' => $admin->getKey()]) }}"
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
		        'required' => true,
		        'type' => 'email',
		        'value' => $admin->email,
		    ],
		    [
		        'name' => '_clients',
		        'title' => 'Управляет клиентами',
		        'required' => true,
		        'type' => 'select',
		        'value' => $clients,
		        'options' => $allclients,
		        'multiple' => true,
		    ],
		    [
		        'name' => 'clients',
		        'type' => 'hidden',
		        'value' => '',
		    ],
		    [
		        'name' => 'password',
		        'title' => 'Новый пароль',
		        'required' => false,
		        'type' => 'password',
		        'generate' => true,
		    ],
		    [
		        'name' => 'password_confirmation',
		        'title' => 'Повторный ввод пароля',
		        'required' => false,
		        'type' => 'password',
		    ],
		];
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

			$('#admin-edit').on('submit', () => {
				if ($('#_clients').val().length == 0) $('#clients').val(null);
				else $('#clients').val(JSON.stringify($('#_clients').val()));
			});
		});
	</script>
@endpush
