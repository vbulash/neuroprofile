@extends('services.service')

@section('service')Работодатель. Начать стажировку практиканта@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор стажировки', 'active' => false, 'context' => 'internship', 'link' => route('e2s.start_internship.step2', ['sid' => session()->getId()])],
			['title' => 'Выбор графика стажировки', 'active' => false, 'context' => 'timetable', 'link' => route('e2s.start_internship.step3', ['sid' => session()->getId()])],
			['title' => 'Выбор практиканта', 'active' => true, 'context' => 'student', 'link' => route('e2s.start_internship.step3', ['sid' => session()->getId()])],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">Просмотр анкеты практиканта &laquo;{{ $student->getTitle() }}&raquo;</h3>
	</div>
	<div class="block-content p-4">
		@php
			$fields = [
				['name' => 'lastname', 'title' => 'Фамилия', 'type' => 'text', 'value' => $student->lastname],
				['name' => 'firstname', 'title' => 'Имя', 'type' => 'text', 'value' => $student->firstname],
				['name' => 'surname', 'title' => 'Отчество', 'type' => 'text', 'value' => $student->surname],
				['name' => 'sex', 'title' => 'Пол', 'type' => 'text', 'value' => $student->sex],
				['name' => 'birthdate', 'title' => 'Дата рождения', 'type' => 'date', 'value' => $student->birthdate],
				['name' => 'phone', 'title' => 'Телефон', 'type' => 'text', 'value' => $student->phone],
				['name' => 'email', 'title' => 'Электронная почта', 'type' => 'email', 'value' => $student->email],
				['name' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать', 'type' => 'textarea', 'value' => $student->parents],
				['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'type' => 'textarea', 'value' => $student->parentscontact],
				['name' => 'passport', 'title' => 'Данные паспорта (серия, номер, кем и когда выдан)', 'type' => 'textarea', 'value' => $student->passport],
				['name' => 'address', 'title' => 'Адрес проживания', 'type' => 'textarea', 'value' => $student->address],
				['name' => 'institutions', 'title' => 'Учебное заведение (на момент заполнения)', 'type' => 'textarea', 'value' => $student->institutions],
				['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'type' => 'text', 'value' => $student->grade],
				['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'type' => 'textarea', 'value' => $student->hobby],
				['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'type' => 'number', 'value' => $student->hobbyyears],
				['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'type' => 'textarea', 'value' => $student->contestachievements],
				['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'type' => 'textarea', 'value' => $student->dream],
			];
		@endphp

		@foreach($fields as $field)
			<div class="row mb-4">
				<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}</label>
				@switch($field['type'])

					@case('text')
					@case('email')
					@case('number')
					@if(isset($field['cast']))
						@php($value = $field['cast']($field['value']))
					@else
						@php($value = $field['value'])
					@endif
					<div class="col-sm-5">
						<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}"
							   value="{{ $value }}" disabled>
					</div>
					@break

					@case('date')
					<div class="col-sm-5">
						<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}" data-date-format="d.m.Y"
							   value="{{ $field['value'] }}"
							   disabled>
					</div>
					@break

					@case('textarea')
					<div class="col-sm-5">
						<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
								  cols="30"
								  rows="5" disabled>{{ $field['value'] }}</textarea>
					</div>
					@break

					@case('editor')
					<div class="col-sm-9">
						<div class="row">
							<div class="document-editor__toolbar"></div>
						</div>
						<div class="row row-editor">
							<div class="editor" id="{{ $field['name'] }}" name="{{ $field['name'] }}"
								 d>{!! $field['value'] !!}</div>
						</div>
					</div>
					@break;
				@endswitch
			</div>
		@endforeach
	</div>

	<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
		<div class="row">
			<div class="col-sm-3 col-form-label">&nbsp;</div>
			<div class="col-sm-5">
				<a class="btn btn-primary pl-3"
				   href="{{ route('e2s.start_internship.step4', ['sid' => session()->getId()]) }}"
				   role="button">Закрыть</a>
			</div>
		</div>
	</div>
@endsection
