@extends('layouts.wizard')

@section('service')Работа с клиентами и контрактами@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Клиент', 'active' => true, 'context' => 'client', 'link' => route('clients.index', ['sid' => session()->getId()])],
			['title' => 'Контракты', 'active' => false, 'context' => 'contract'],
		];
	@endphp
@endsection

@section('interior')
	<form role="form" class="p-5" method="post"
		  id="client-create" name="client-create"
		  action="{{ route('clients.update', ['client' => $client->getKey(), 'sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@method('PUT')
		<div class="block-header block-header-default">
			<h3 class="block-title fw-semibold">
				@if($show) Просмотр @else Редактирование @endif анкеты клиента &laquo;{{ $client->name }}&raquo;
				@if(!$show)
					<br/>
					<small><span class="required">*</span> - поля, обязательные для заполнения</small>
				@endif
			</h3>
		</div>
		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'name', 'title' => 'Наименование клиента', 'required' => true, 'type' => 'text', 'value' => $client->name],
					['name' => 'inn', 'title' => 'ИНН клиента', 'required' => true, 'type' => 'text', 'value' => $client->inn],
					['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП клиента', 'required' => true, 'type' => 'text', 'value' => $client->ogrn],
					['name' => 'address', 'title' => 'Адрес', 'required' => true, 'type' => 'textarea', 'value' => $client->address],
					['name' => 'phone', 'title' => 'Телефон', 'required' => false, 'type' => 'text', 'value' => $client->phone],
					['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $client->email],
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">
						{{ $field['title'] }} @if($field['required'] && !$show) <span class="required">*</span> @endif
					</label>
					<div class="col-sm-5">
						@switch($field['type'])

							@case('text')
							@case('email')
							@case('number')
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ old($field['name'], $field['value']) }}"
								   @if($show) disabled @endif
							>
							@break

							@case('date')
							<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" data-date-format="d.m.Y"
								   value="{{ old($field['name'], $field['value']) }}"
								   @if($show) disabled @endif
							>
							@break

							@case('textarea')
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30" rows="5"
									  @if($show) disabled @endif
							>{{ old($field['name'], $field['value']) }}</textarea>
							@break
						@endswitch
					</div>
				</div>
			@endforeach
		</div>

		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				<div class="col-sm-5">
					@if($show)
						<a class="btn btn-primary pl-3"
						   href="{{ route('clients.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@else
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<a class="btn btn-secondary pl-3"
					   href="{{ route('clients.index', ['sid' => session()->getId()]) }}"
					   role="button">Закрыть</a>
					@endif
				</div>
			</div>
		</div>
	</form>
@endsection
