@extends('services.service')

@section('service')Работодатель. Начать стажировку практиканта@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => true, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор стажировки', 'active' => false, 'context' => 'internship'],
			['title' => 'Выбор графика стажировки', 'active' => false, 'context' => 'timetable'],
			['title' => 'Выбор практиканта', 'active' => false, 'context' => 'student'],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">Просмотр анкеты работодателя &laquo;{{ $employer->name }}&raquo;
		</h3>
	</div>
	<div class="block-content p-4">
		@php
			$fields = [
				['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'text', 'value' => $employer->name],
				['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text', 'value' => $employer->contact],
				['name' => 'address', 'title' => 'Фактический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->address],
				['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $employer->phone],
				['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text', 'value' => $employer->email],
				['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text', 'value' => $employer->inn],
				['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text', 'value' => $employer->kpp],
				['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => false, 'type' => 'text', 'value' => $employer->ogrn],
				['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->official_address],
				['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text', 'value' => $employer->post_address],
				['name' => 'description', 'title' => 'Краткое описание организации (основная деятельность)', 'required' => false, 'type' => 'textarea', 'value' => $employer->description],
				['name' => 'expectation', 'title' => 'Какие результаты ожидаются от практикантов / выпускников?', 'required' => false, 'type' => 'textarea', 'value' => $employer->expectation],
				// TODO: реализовать browse_multiple (elFinder?) для хранения документов
				//['name' => 'nda', 'title' => 'Соглашение о неразглашении информации', 'required' => false, 'type' => 'text'],
			];
		@endphp

		@foreach($fields as $field)
			<div class="row mb-4">
				<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}</label>
				<div class="col-sm-5">
					@switch($field['type'])

						@case('text')
						@case('email')
						@case('number')
						<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}"
							   value="{{ $field['value'] }}" disabled>
						@break

						@case('date')
						<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}" data-date-format="d.m.Y"
							   value="{{ $field['value'] }}"
							   disabled>
						@break

						@case('textarea')
						<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
								  cols="30"
								  rows="5" disabled>{{ $field['value'] }}</textarea>
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
				<a class="btn btn-primary pl-3"
				   href="{{ route('e2s.start_internship.step1', ['sid' => session()->getId()]) }}"
				   role="button">Закрыть</a>
			</div>
		</div>
	</div>
@endsection
