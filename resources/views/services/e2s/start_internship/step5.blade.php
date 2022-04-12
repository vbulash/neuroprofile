@extends('services.service')

@section('service')Работодатель. Начать стажировку практиканта@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор стажировки', 'active' => false, 'context' => 'internship', 'link' => route('e2s.start_internship.step2', ['sid' => session()->getId()])],
			['title' => 'Выбор графика стажировки', 'active' => false, 'context' => 'timetable', 'link' => route('e2s.start_internship.step3', ['sid' => session()->getId()])],
			['title' => 'Выбор практиканта', 'active' => false, 'context' => 'student', 'link' => route('e2s.start_internship.step4', ['sid' => session()->getId()])],
			['title' => 'Подтверждение выбора', 'active' => true],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			Подтверждение начала стажировки<br/>
			<small>Выбраны следующие параметры прохождения стажировки:</small>
		</h3>
	</div>
	<div class="block-content p-4">
		@php
			$context = session('context');
			$fields = [
				['name' => 'employer', 'title' => 'Работодатель'],
				['name' => 'internship', 'title' => 'Стажировка'],
				['name' => 'timetable', 'title' => 'График стажировки'],
				['name' => 'student', 'title' => 'Практикант'],
			];
		@endphp

		@foreach($fields as $field)
			<div class="row mb-4">
				<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}</label>
				<div class="col-sm-5">
					<input type="text" class="form-control" id="{{ $field['name'] }}" name="{{ $field['name'] }}"
						   value="{{ $context[$field['name']]->getTitle() }}" disabled>
				</div>
			</div>
		@endforeach

		<p>Начать стажировку практиканта?</p>
		<p>
			Нажатие &laquo;Да&raquo; зарегистрирует стажировку практиканта.<br/>
			Нажатие &laquo;Нет&raquo; вернет вас на главную страницу сайта для выбора услуг
		</p>
	</div>

	<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
		<div class="row">
			<div class="col-sm-3 col-form-label">&nbsp;</div>
			<div class="col-sm-5">
				<a class="btn btn-primary pl-3"
				   href="{{ route('e2s.start_internship.step5.create', ['sid' => session()->getId()]) }}"
				   role="button">Да</a>
				<a class="btn btn-secondary pl-3"
				   href="{{ route('dashboard', ['sid' => session()->getId()]) }}"
				   role="button">Нет</a>
			</div>
		</div>
	</div>
@endsection
