@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@if($show) Просмотр анкеты @else Анкета @endif
					практиканта
					&laquo;{{ sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : '')) }}&raquo;</h1>
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
				@if(!$show)
					<div class="row pt-5 ps-5 pe-5">
						<p><span class="required">*</span> - поля, обязательные для заполнения</p>
					</div>
				@endif
				@include('students.assign')
			</div>

			<form role="form" class="p-5" method="post"
				  id="student-edit" name="student-edit"
				  action="{{ route('students.update', ['student' => $student->id, 'sid' => session()->getId()]) }}"
				  autocomplete="off" enctype="multipart/form-data">
				@method('PUT')
				@csrf

				@php
					$fields = [
						['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text', 'value' => $student->lastname],
						['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text', 'value' => $student->firstname],
						['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text', 'value' => $student->surname],
						['name' => 'sex', 'title' => 'Пол', 'required' => true, 'type' => 'select', 'value' => $student->sex, 'options' => ['Мужской', 'Женский']],
						['name' => 'birthdate', 'title' => 'Дата рождения', 'required' => true, 'type' => 'date', 'value' => $student->birthdate],
						['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $student->phone],
						['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $student->email],
						['name' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать', 'required' => false, 'type' => 'textarea', 'value' => $student->parents],
						['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'required' => false, 'type' => 'textarea', 'value' => $student->parentscontact],
						['name' => 'passport', 'title' => 'Данные паспорта (серия, номер, кем и когда выдан)', 'required' => false, 'type' => 'textarea', 'value' => $student->passport],
						['name' => 'address', 'title' => 'Адрес проживания', 'required' => false, 'type' => 'textarea', 'value' => $student->address],
						['name' => 'institutions', 'title' => 'Учебное заведение (на момент заполнения)', 'required' => false, 'type' => 'textarea', 'value' => $student->institutions],
						['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'required' => false, 'type' => 'text', 'value' => $student->grade],
						['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'required' => false, 'type' => 'textarea', 'value' => $student->hobby],
						['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'required' => false, 'type' => 'number', 'value' => $student->hobbyyears],
						['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'required' => false, 'type' => 'textarea', 'value' => $student->contestachievements],
						['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'required' => false, 'type' => 'textarea', 'value' => $student->dream],
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
								@if($field['required'] && !$show) <span class="required">*</span> @endif</label>
							@break
						@endswitch
						<div class="col-sm-5">
							@switch($field['type'])

								@case('text')
								@case('email')
								@case('number')
								<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}"
									   value="{{ $field['value'] }}" @if($show) disabled @endif>
								@break

								@case('select')
								<div>
									<select class="form-control select2" name="{{ $field['name'] }}"
											id="{{ $field['name'] }}" @if($show) disabled @endif>
										@foreach($field['options'] as $option)
											<option value="{{ $option }}"
													@if($field['value'] == $option) selected @endif>{{ $option }}</option>
										@endforeach
									</select>
								</div>
								@break

								@case('date')
								<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}" data-date-format="d.m.Y"
									   value="{{ $field['value'] }}"
									   @if($show) disabled @endif>
								@break

								@case('textarea')
								<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
										  cols="30"
										  rows="5" @if($show) disabled @endif>{{ $field['value'] }}</textarea>
								@break

								@case('hidden')
								<input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}">
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
						@if(!$show)
							<button type="submit" class="btn btn-primary">Сохранить</button>
						@endif
						<a class="btn @if($show) btn-primary @else btn-secondary @endif pl-3"
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
		document.getElementById("student-edit").addEventListener("submit", () => {
			let link = document.getElementById("link").value;
			document.getElementById("user_id").value = link;
		}, false);
	</script>
@endpush
