@extends('layouts.wizard')

@section('service')Работа с клиентами и контрактами@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Клиент', 'active' => false, 'context' => 'client', 'link' => route('clients.index', ['sid' => session()->getId()])],
			['title' => 'Контракты', 'active' => true, 'context' => 'contract', 'link' => route('contracts.index', ['sid' => session()->getId()])],
			['title' => 'Информация о контракте', 'active' => false, 'context' => 'info'],
		];
	@endphp
@endsection

@section('interior')
	<form role="form" class="p-5" method="post"
		  id="client-create" name="client-create"
		  action="{{ route('contracts.update', ['contract' => $contract->getKey(), 'sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@method('PUT')
		<div class="block-header block-header-default">
			<h3 class="block-title fw-semibold">
				@if($show) Просмотр @else Редактирование @endif контракта № {{ $contract->number }} клиента &laquo;{{ $contract->client->name }}&raquo;
				@if(!$show)
					<br/>
					<small><span class="required">*</span> - поля, обязательные для заполнения</small>
				@endif
			</h3>
		</div>
		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'number', 'title' => 'Номер контракта', 'required' => true, 'type' => 'text', 'value' => $contract->number],
					['name' => 'start', 'title' => 'Дата начала контракта', 'required' => true, 'type' => 'date', 'value' => $contract->start->format('d.m.Y')],
					['name' => 'end', 'title' => 'Дата завершения контракта', 'required' => true, 'type' => 'date', 'value' => $contract->end->format('d.m.Y')],
					['name' => 'invoice', 'title' => 'Номер оплаченного счета', 'required' => true, 'type' => 'text', 'value' => $contract->invoice],
					['name' => 'license_count', 'title' => 'Количество лицензий контракта', 'required' => true, 'type' => 'number', 'value' => $contract->license_count],
					['name' => 'url', 'title' => 'URL страницы сайта клиента', 'required' => true, 'type' => 'text', 'value' => $contract->url],
					['name' => 'mkey', 'title' => 'Мастер-ключ контракта', 'type' => 'text', 'required' => false, 'value' => $contract->mkey, 'disabled' => true],
					['name' => 'contract_id', 'type' => 'hidden', 'value' => $contract->getKey()],
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					@switch($field['type'])
						@case('hidden')
						@break

						@default
						<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
							@if(!$show && $field['required']) <span class="required">*</span> @endif</label>
						@break
					@endswitch

					@switch($field['type'])

						@case('text')
						@case('email')
						@case('number')
						<div class="col-sm-5">
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ old($field['name'], $field['value']) }}"
								   @if($show || isset($field['disabled'])) disabled @endif
							>
						</div>
						@break

						@case('date')
						<div class="col-sm-5">
							<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" data-date-format="d.m.Y"
								   value="{{ old($field['name'], $field['value']) }}"
								   @if($show || isset($field['disabled'])) disabled @endif
							>
						</div>
						@break

						@case('textarea')
						<div class="col-sm-5">
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30" rows="5"
									  @if($show || isset($field['disabled'])) disabled @endif
							>{{ old($field['name'], $field['value']) }}</textarea>
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
					@if($show)
						<a class="btn btn-primary pl-3"
						   href="{{ route('contracts.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@else
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('contracts.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@endif
				</div>
			</div>
		</div>
	</form>
@endsection
