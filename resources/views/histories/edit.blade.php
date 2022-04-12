@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@if($show) Просмотр @else Редактирование @endif
					записи истории стажировок № {{ $history->getKey() }}</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Стажировки</li>
						<li class="breadcrumb-item active" aria-current="page">Истории стажировок</li>
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
			</div>

			<form role="form" class="p-5" method="post"
				  id="student-edit" name="student-edit"
				  action="{{ route('history.update', ['history' => $history->getKey(), 'sid' => session()->getId()]) }}"
				  autocomplete="off" enctype="multipart/form-data">
				@method('PUT')
				@csrf

				@php
					$fields = [
						['name' => 'employer', 'title' => 'Работодатель', 'required' => false, 'type' => 'text', 'value' => $history->timetable->internship->employer->getTitle(), 'disabled' => true],
						['name' => 'internship', 'title' => 'Стажировка', 'required' => false, 'type' => 'text', 'value' => $history->timetable->internship->getTitle(), 'disabled' => true],
						['name' => 'timetable', 'title' => 'График стажировки', 'required' => false, 'type' => 'text', 'value' => $history->timetable->getTitle(), 'disabled' => true],
						['name' => 'student', 'title' => 'Практикант', 'required' => false, 'type' => 'text', 'value' => $history->student->getTitle(), 'disabled' => true],
						['name' => 'status', 'title' => 'Статус стажировки', 'required' => true, 'type' => 'select', 'value' => $history->status, 'options' => ['Планируется', 'Выполняется', 'Закрыта']],
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
									   value="{{ $field['value'] }}" @if($show || $field['disabled']) disabled @endif>
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
						   href="{{ route('history.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

