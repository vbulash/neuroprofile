@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@if($show) Просмотр @else Редактирование @endif записи графика стажировки<br/>
					Стажировка &laquo;{{ $timetable->internship->iname }}&raquo; работодателя &laquo;{{ $timetable->internship->employer->name }}&raquo;
				</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Лица</li>
						<li class="breadcrumb-item">Работодатели</li>
						<li class="breadcrumb-item">Стажировки</li>
						<li class="breadcrumb-item active" aria-current="page">График стажировки</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<div class="block block-rounded">
			@if(!$show)
				<div class="block-header-default">
					<div class="row pt-5 ps-5 pe-5">
						<p><span class="required">*</span> - поля, обязательные для заполнения</p>
					</div>
				</div>
			@endif
			<form role="form" class="p-5" method="post"
				  id="timetable-create" name="timetable-create"
				  action="{{ route('timetables.update', ['timetable' => $timetable->getKey(), 'sid' => session()->getId()]) }}"
				  autocomplete="off" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<input type="hidden" name="internship_id" id="internship_id" value="{{ $timetable->internship->getKey() }}">

				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="start">Начало @if(!$show) <span
							class="required">*</span>@endif</label>
					<div class="col-sm-5">
						<input type="text" class="flatpickr-input form-control" id="start" name="start"
							   data-date-format="d.m.Y" placeholder="Выберите дату" value="{{ $timetable->start }}" @if($show) disabled @endif>
					</div>
				</div>

				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="end">Завершение @if(!$show) <span
							class="required">*</span>@endif</label>
					<div class="col-sm-5">
						<input type="text" class="flatpickr-input form-control" id="end" name="end"
							   data-date-format="d.m.Y" placeholder="Выберите дату" value="{{ $timetable->end }}" @if($show) disabled @endif>
					</div>
				</div>

				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="name">Наименование записи графика стажировки</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="name" name="name" value="{{ $timetable->name }}" @if($show) disabled @endif>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-3 col-form-label">&nbsp;</div>
					<div class="col-sm-5">
						@if($show)
							<a class="btn btn-primary pl-3"
							   href="{{ route('timetables.index', ['internship' => $timetable->internship->getKey(), 'sid' => session()->getId()]) }}"
							   role="button">Закрыть</a>
						@else
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('timetables.index', ['internship' => $timetable->internship->getKey(), 'sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
						@endif
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

