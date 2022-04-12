@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">График стажировки<br/>
					Стажировка &laquo;{{ $internship->iname }}&raquo; работодателя &laquo;{{ $internship->employer->name }}&raquo;</h1>
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
		<!-- Table -->
		<div class="block block-rounded">
			<div class="block-header block-header-default">
				<a href="{{ route('timetables.create', ['internship' => $internship->getKey(), 'sid' => session()->getId()]) }}"
				   class="btn btn-primary mt-3 mb-3">Добавить запись графика стажировки</a>
			</div>
			<div class="block-content pb-3">
				@if ($count > 0)
					<div class="table-responsive">
						<table class="table table-bordered table-hover text-nowrap" id="timetables_table"
							   style="width: 100%;">
							<thead>
							<tr>
								<th style="width: 30px">#</th>
								<th>Начало</th>
								<th>Завершение</th>
								<th>Наименование записи графика стажировки</th>
								<th>Действия</th>
							</tr>
							</thead>
						</table>
					</div>
				@else
					<p>Графиков стажировки пока нет...</p>
				@endif
			</div>
		</div>
		<!-- END Table -->
	</div>

@endsection

@if ($count > 0)
@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>

		document.getElementById('confirm-yes').addEventListener('click', (event) => {
			$.ajax({
				method: 'DELETE',
				url: "{{ route('timetables.destroy', ['timetable' => '0']) }}",
				data: {
					id: event.target.dataset.id,
				},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: () => {
					window.datatable.ajax.reload();
				}
			});
		}, false);

		function clickDelete(id, name) {
			document.getElementById('confirm-title').innerText = "Подтвердите удаление";
			document.getElementById('confirm-body').innerHTML = "Удалить запись графика стажировки № " + id + " ?";
			document.getElementById('confirm-yes').dataset.id = id;
			let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
			confirmDialog.show();
		}

		$(function () {
			window.datatable = $('#timetables_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('timetables.index.data', ['internship' => $internship->getKey()]) !!}',
				responsive: true,
				columns: [
					{data: 'id', name: 'id', responsivePriority: 1},
					{data: 'start', name: 'start', responsivePriority: 2},
					{data: 'end', name: 'end', responsivePriority: 2},
					{data: 'name', name: 'name', responsivePriority: 3},
					{
						data: 'action',
						name: 'action',
						sortable: false,
						responsivePriority: 1,
						className: 'no-wrap dt-actions'
					}
				]
			});
		});
	</script>
@endpush
@endif
