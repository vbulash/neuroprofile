@extends('layouts.single')

@section('title') Истории стажировки @endsection

@php
	$breadcrumbs = [
    	'Стажировки',
    	'Истории стажировки'
	];
@endphp

@section('header')
	@if(isset($ids))
		<div class="block-header block-header-default">
			<p class="mt-auto mb-auto">Отображаются только записи истории стажировок, доступные текущему
				пользователю</p>
		</div>
	@endif
@endsection

@section('interior')
	@if ($count > 0)
		<div class="table-responsive">
			<table class="table table-bordered table-hover text-nowrap" id="histories_table"
				   style="width: 100%;">
				<thead>
				<tr>
					<th style="width: 30px">#</th>
					<th>Работодатель</th>
					<th>Стажировка</th>
					<th>График стажировки</th>
					<th>Практикант</th>
					<th>Статус</th>
					<th>Действия</th>
				</tr>
				</thead>
			</table>
		</div>
	@else
		<p>Записей историй стажировок пока нет...</p>
	@endif
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
					url: "{{ route('history.destroy', ['history' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить запись истории стажировки № " + id + " ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#histories_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					@if(isset($ids))
					ajax: '{!! route('history.index.data', ['ids' => $ids]) !!}',
					@else
					ajax: '{!! route('history.index.data') !!}',
					@endif
					responsive: true,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'employer', name: 'employer', responsivePriority: 1},
						{data: 'internship', name: 'internship', responsivePriority: 3},
						{data: 'timetable', name: 'timetable', responsivePriority: 2},
						{data: 'student', name: 'student', responsivePriority: 2},
						{
							data: 'status', name: 'status', responsivePriority: 2, render: (data) => {
								switch (data) {
									case 'Планируется':
										return "<b><span style='color: red;'>" + data + "</span></b>";
									default:
										return data;
								}
							}
						},
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
