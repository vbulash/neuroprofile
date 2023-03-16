@extends('layouts.detail')

@section('service')
	Работа с клиентами и контрактами
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Клиент', 'active' => false, 'context' => 'client', 'link' => route('clients.index')], ['title' => 'Контракт', 'active' => true, 'context' => 'contract', 'link' => route('clients.index')], ['title' => 'Информация о контракте', 'active' => false, 'context' => 'info']];
	@endphp
@endsection

@section('interior.header')
	Новый контракт<br />
	<small>При сохранении нового контракта произойдёт генерация мастер-ключа и лицензий с персональными ключами</small>
@endsection

@section('form.params')
	id="{{ form(\App\Models\Contract::class, $mode, 'id') }}" name="{{ form(\App\Models\Contract::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Contract::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
		    [
		        'name' => 'number',
		        'title' => 'Номер контракта',
		        'required' => true,
		        'type' => 'text',
		    ],
		    ['name' => 'start', 'title' => 'Дата начала контракта', 'required' => true, 'type' => 'date'],
		    ['name' => 'end', 'title' => 'Дата завершения контракта', 'required' => true, 'type' => 'date'],
		    ['name' => 'invoice', 'title' => 'Номер оплаченного счета', 'required' => true, 'type' => 'text'],
		    ['name' => 'email', 'title' => 'Электронная почта контракта', 'required' => false, 'type' => 'email', 'placeholder' => 'Если отсутствует - используется электронная почта клиента'],
		    ['name' => 'commercial', 'title' => 'Коммерческий контракт', 'required' => false, 'type' => 'checkbox', 'value' => true],
		    ['name' => 'license_count', 'title' => 'Количество лицензий контракта', 'required' => true, 'type' => 'number', 'min' => '1'],
		    ['name' => 'url', 'title' => 'URL страницы сайта клиента', 'required' => true, 'type' => 'text'],
		    ['name' => 'client_id', 'type' => 'hidden', 'value' => $client->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Contract::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let commercial = document.getElementById('commercial');
		commercial.addEventListener('change', (event) => {
			if (event.target.checked) { // Коммерческий контракт
				event.target.parentElement.querySelector('label').innerText = 'Коммерческий контракт';
			} else { // Некоммерческий контракт
				event.target.parentElement.querySelector('label').innerText = 'Некоммерческий контракт';
			}
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			commercial.dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
