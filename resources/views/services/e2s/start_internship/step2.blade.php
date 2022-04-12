@extends('services.service')

@section('service')Работодатель. Начать стажировку практиканта@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор стажировки', 'active' => true, 'context' => 'internship'],
			['title' => 'Выбор графика стажировки', 'active' => false, 'context' => 'timetable'],
			['title' => 'Выбор практиканта', 'active' => false, 'context' => 'student'],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			Выбор стажировки у работодателя &laquo;{{ $employer->name }}&raquo;<br/>
			<small>Показываются только незакрытые стажировки</small>
		</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="internships_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Название стажировки</th>
						<th>Тип</th>
						<th>Статус</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Стажировок пока нет...</p>
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
				window.datatable = $('#internships_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('e2s.start_internship.step2.data') !!}',
					responsive: true,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'iname', name: 'iname', responsivePriority: 2},
						{data: 'itype', name: 'itype', responsivePriority: 3},
						{data: 'status', name: 'status', responsivePriority: 3},
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
