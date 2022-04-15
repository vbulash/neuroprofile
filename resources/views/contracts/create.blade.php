@extends('layouts.wizard')

@section('service')Работа с клиентами и контрактами@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Клиент', 'active' => false, 'context' => 'client', 'link' => route('clients.index', ['sid' => session()->getId()])],
			['title' => 'Контракт', 'active' => true, 'context' => 'contract', 'link' => route('clients.index', ['sid' => session()->getId()])],
			['title' => 'Информация о контракте', 'active' => false, 'context' => 'info'],
		];
	@endphp
@endsection

@section('interior')
	<form role="form" class="p-5" method="post"
		  id="client-create" name="client-create"
		  action="{{ route('contracts.store', ['sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		<div class="block-header block-header-default">
			<h3 class="block-title fw-semibold">
				Создание контракта для клиента &laquo;{{ $client->name }}&raquo;.<br/>
				При сохранении нового контракта произойдёт генерация мастер-ключа и лицензий с персональными
				ключами.<br/>
				<small><span class="required">*</span> - поля, обязательные для заполнения</small>
			</h3>
		</div>
		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'number', 'title' => 'Номер контракта', 'required' => true, 'type' => 'text'],
					['name' => 'start', 'title' => 'Дата начала контракта', 'required' => true, 'type' => 'date'],
					['name' => 'end', 'title' => 'Дата завершения контракта', 'required' => true, 'type' => 'date'],
					['name' => 'invoice', 'title' => 'Номер оплаченного счета', 'required' => true, 'type' => 'text'],
					['name' => 'license_count', 'title' => 'Количество лицензий контракта', 'required' => true, 'type' => 'number'],
					['name' => 'url', 'title' => 'URL страницы сайта клиента', 'required' => true, 'type' => 'text'],
					['name' => 'client_id', 'type' => 'hidden', 'value' => $client->getKey()],
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					@switch($field['type'])
						@case('hidden')
						@break

						@default
						<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
							@if($field['required']) <span class="required">*</span> @endif</label>
						@break
					@endswitch

					@switch($field['type'])

						@case('text')
						@case('email')
						@case('number')
						<div class="col-sm-5">
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ old($field['name']) }}">
						</div>
						@break

						@case('date')
						<div class="col-sm-5">
							<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" data-date-format="d.m.Y"
								   value="{{ old($field['name']) }}">
						</div>
						@break

						@case('textarea')
						<div class="col-sm-5">
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30" rows="5">{{ old($field['name']) }}</textarea>
						</div>
						@break

						@case('hidden')
						<input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}" value="{{ $field['value'] }}">
						@break
					@endswitch
				</div>
			@endforeach
		</div>

		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				<div class="col-sm-5">
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<a class="btn btn-secondary pl-3"
					   href="{{ route('contracts.index', ['sid' => session()->getId()]) }}"
					   role="button">Закрыть</a>
				</div>
			</div>
		</div>
	</form>
@endsection
