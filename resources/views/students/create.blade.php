@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Новый практикант</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Лица</li>
						<li class="breadcrumb-item active" aria-current="page">Практиканты</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<div class="block block-rounded">
			<div class="block-header-default">
				<div class="row pt-5 ps-5 pe-5">
					<p><span class="required">*</span> - поля, обязательные для заполнения</p>
				</div>
				@include('students.assign')
			</div>
			<form role="form" class="p-5" method="post"
				  id="student-create" name="student-create"
				  action="{{ route('students.store', ['sid' => session()->getId()]) }}"
				  autocomplete="off" enctype="multipart/form-data">
				@csrf

				@php
					$fields = [
						['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text'],
						['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text'],
						['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text'],
						['name' => 'sex', 'title' => 'Пол', 'required' => true, 'type' => 'select', 'options' => ['Выберите пол', 'Мужской', 'Женский']],
						['name' => 'birthdate', 'title' => 'Дата рождения', 'required' => true, 'type' => 'date'],
						['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text'],
						['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'],
						['name' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать', 'required' => false, 'type' => 'textarea'],
						['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'required' => false, 'type' => 'textarea'],
						['name' => 'passport', 'title' => 'Данные паспорта (серия, номер, кем и когда выдан)', 'required' => false, 'type' => 'textarea'],
						['name' => 'address', 'title' => 'Адрес проживания', 'required' => false, 'type' => 'textarea'],
						['name' => 'institutions', 'title' => 'Учебное заведение (на момент заполнения)', 'required' => false, 'type' => 'textarea'],
						['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'required' => false, 'type' => 'text'],
						['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'required' => false, 'type' => 'textarea'],
						['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'required' => false, 'type' => 'number'],
						['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'required' => false, 'type' => 'textarea'],
						['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'required' => false, 'type' => 'textarea'],
						['name' => 'user_id', 'type' => 'hidden']
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
						<div class="col-sm-5">
							@switch($field['type'])

								@case('text')
								@case('email')
								@case('number')
								<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}">
								@break

								@case('select')
								{{--								<div class="input-group input-group-lg">--}}
								<select class="form-control select2" name="{{ $field['name'] }}"
										id="{{ $field['name'] }}">
									@foreach($field['options'] as $option)
										<option value="{{ $option }}"
												@if($loop->first) selected disabled @endif>{{ $option }}</option>
									@endforeach
								</select>
								{{--									<span class="input-group-text"><i class="fa fa-chevron-down"></i></span>--}}
								{{--								</div>--}}
								@break

								@case('date')
								<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}" data-date-format="d.m.Y">
								@break

								@case('textarea')
								<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
										  cols="30"
										  rows="5"></textarea>
								@break

								@case('hidden')
								<input type="{{ $field['type'] }}" id="{{ $field['name'] }}" name="{{ $field['name'] }}">
								@break
							@endswitch
						</div>
					</div>
				@endforeach

				{{--					TODO: реализовать browse_multiple (elFinder?) для хранения документов --}}
				{{-- $this->crud->field('documents')->label('Документы')->type('browse_multiple'); --}}

				<div class="row">
					<div class="col-sm-3 col-form-label">&nbsp;</div>
					<div class="col-sm-5">
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('students.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

@push('js_after')
	<script>
		document.getElementById("student-create").addEventListener("submit", () => {
			let link = document.getElementById("link").value;
			document.getElementById("user_id").value = link;
		}, false);
	</script>
@endpush
