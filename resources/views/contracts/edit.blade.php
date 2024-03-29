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
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif контракта № {{ $contract->number }}
@endsection

@section('form.params')
	id="{{ form($contract, $mode, 'id') }}" name="{{ form($contract, $mode, 'name') }}"
	action="{{ form($contract, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
		    ['name' => 'number', 'title' => 'Номер контракта', 'required' => true, 'type' => 'text', 'value' => $contract->number],
		    ['name' => 'start', 'title' => 'Дата начала контракта', 'required' => true, 'type' => 'date', 'value' => $contract->start->format('d.m.Y')],
		    ['name' => 'end', 'title' => 'Дата завершения контракта', 'required' => true, 'type' => 'date', 'value' => $contract->end->format('d.m.Y')],
		    [
		        'name' => 'status',
		        'title' => 'Статус контракта',
		        'required' => false,
		        'type' => 'text',
		        'disabled' => true,
		        'value' => match ($contract->status) {
		            \App\Models\Contract::INACTIVE => 'Неактивен (дата начала в будущем)',
		            \App\Models\Contract::ACTIVE => 'Исполняется',
		            \App\Models\Contract::COMPLETE_BY_DATE => 'Завершен по дате',
		            \App\Models\Contract::COMPLETE_BY_COUNT => 'Закончились свободные лицензии',
		        },
		    ],
		    ['name' => 'invoice', 'title' => 'Номер оплаченного счета', 'required' => true, 'type' => 'text', 'value' => $contract->invoice],
		    ['name' => 'email', 'title' => 'Электронная почта контракта', 'required' => false, 'type' => 'email', 'placeholder' => 'Если отсутствует - используется электронная почта клиента', 'value' => $contract->email],
		    ['name' => 'commercial', 'title' => 'Коммерческий контракт', 'required' => false, 'type' => 'checkbox', 'value' => $contract->commercial],
		    ['name' => 'license_count', 'title' => 'Количество лицензий контракта', 'required' => true, 'type' => 'number', 'value' => $contract->license_count],
		    ['name' => 'url', 'title' => 'URL страницы сайта клиента', 'required' => true, 'type' => 'text', 'value' => $contract->url],
		    ['name' => 'mkey', 'title' => 'Мастер-ключ контракта', 'type' => 'text', 'required' => false, 'value' => $contract->mkey, 'disabled' => true],
		    ['name' => 'contract_id', 'type' => 'hidden', 'value' => $contract->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($contract, $mode, 'close') }}
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
