@extends('services.service')

@section('service')Работодатель. Начать стажировку практиканта@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор стажировки', 'active' => false, 'context' => 'internship', 'link' => route('e2s.start_internship.step2', ['sid' => session()->getId()])],
			['title' => 'Выбор графика стажировки', 'active' => true, 'context' => 'timetable', 'link' => route('e2s.start_internship.step3', ['sid' => session()->getId()])],
			['title' => 'Выбор практиканта', 'active' => false, 'context' => 'student'],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">Просмотр графика стажировки для стажировки &laquo;{{ $timetable->internship->iname }}&raquo;
			у работодателя &laquo;{{ $timetable->internship->employer->name }}&raquo;</h3>
	</div>
	<div class="block-content p-4">
		@php
			$fields = [
				['name' => 'start', 'title' => 'Начало', 'type' => 'date', 'value' => $timetable->start],
				['name' => 'end', 'title' => 'Завершение', 'type' => 'date', 'value' => $timetable->end],
				['name' => 'name', 'title' => 'Наименование записи графика стажировки', 'type' => 'text', 'value' => $timetable->name],
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
				   href="{{ route('e2s.start_internship.step3', ['sid' => session()->getId()]) }}"
				   role="button">Закрыть</a>
			</div>
		</div>
	</div>
@endsection
