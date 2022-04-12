@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@if($show) Просмотр анкеты @else Анкета @endif
					работодателя &laquo;{{ $employer->name }}&raquo;</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Лица</li>
						<li class="breadcrumb-item active" aria-current="page">Работодатели</li>
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
				@include('employers.assign')
			</div>

			<form role="form" class="p-5" method="post"
				  id="employer-edit" name="employer-edit"
				  action="{{ route('employers.update', ['employer' => $employer->id, 'sid' => session()->getId()]) }}"
				  autocomplete="off" enctype="multipart/form-data">
				@method('PUT')
				@csrf

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
						['name' => 'user_id', 'type' => 'hidden'],
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

				<div class="row">
					<div class="col-sm-3 col-form-label">&nbsp;</div>
					<div class="col-sm-5">
						@if(!$show)
							<button type="submit" class="btn btn-primary">Сохранить</button>
						@endif
						<a class="btn @if($show) btn-primary @else btn-secondary @endif pl-3"
						   href="{{ route('employers.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					</div>
				</div>
			</form>
		</div>
	</div>

@endsection

@push('js_after')
	<script>
		document.getElementById("employer-edit").addEventListener("submit", () => {
			let link = document.getElementById("link").value;
			document.getElementById("user_id").value = link;
		}, false);
	</script>
@endpush
