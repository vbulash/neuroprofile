@extends('services.service')

@section('service')Работодатель. Начать стажировку практиканта@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор стажировки', 'active' => false, 'context' => 'internship', 'link' => route('e2s.start_internship.step2', ['sid' => session()->getId()])],
			['title' => 'Выбор графика стажировки', 'active' => true, 'context' => 'timetable'],
			['title' => 'Выбор практиканта', 'active' => false, 'context' => 'student'],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			Выбор графика стажировки по стажировке &laquo;{{ $internship->iname }}&raquo;
			у работодателя &laquo;{{ $internship->employer->name }}&raquo;
		</h3>
	</div>
	<div class="block-content p-4">
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
@endsection

@if ($count > 0)
	@push('css_after')
		<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
	@endpush

	@push('js_after')
		<script src="{{ asset('js/datatables.js') }}"></script>
		<script>
			$(function () {
				window.datatable = $('#timetables_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('e2s.start_internship.step3.data') !!}',
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
