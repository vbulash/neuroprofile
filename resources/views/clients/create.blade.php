@extends('layouts.wizard')

@section('service')Работа с клиентами и контрактами@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Клиент', 'active' => true, 'context' => 'client', 'link' => route('clients.index', ['sid' => session()->getId()])],
			['title' => 'Контракт', 'active' => false, 'context' => 'contract'],
			['title' => 'Информация о контракте', 'active' => false, 'context' => 'info'],
		];
	@endphp
@endsection

@section('interior')
	<form role="form" class="p-5" method="post"
		  id="client-create" name="client-create"
		  action="{{ route('clients.store', ['sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		<div class="block-header block-header-default">
			<h3 class="block-title fw-semibold">
				Создание клиента<br/>
				<small><span class="required">*</span> - поля, обязательные для заполнения</small>
			</h3>
		</div>
		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'name', 'title' => 'Наименование клиента', 'required' => true, 'type' => 'text'],
					['name' => 'inn', 'title' => 'ИНН клиента', 'required' => true, 'type' => 'text'],
					['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП клиента', 'required' => true, 'type' => 'text'],
					['name' => 'address', 'title' => 'Адрес', 'required' => true, 'type' => 'textarea'],
					['name' => 'phone', 'title' => 'Телефон', 'required' => false, 'type' => 'text'],
					['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'],
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">
						{{ $field['title'] }} @if($field['required']) <span class="required">*</span> @endif
					</label>
					<div class="col-sm-5">
						@switch($field['type'])

							@case('text')
							@case('email')
							@case('number')
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ old($field['name']) }}">
							@break

							@case('date')
							<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" data-date-format="d.m.Y" value="{{ old($field['name']) }}">
							@break

							@case('textarea')
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30" rows="5">{{ old($field['name']) }}</textarea>
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
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<a class="btn btn-secondary pl-3"
					   href="{{ route('clients.index', ['sid' => session()->getId()]) }}"
					   role="button">Закрыть</a>
				</div>
			</div>
		</div>
	</form>
@endsection
