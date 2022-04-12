@extends('services.service')

@section('service')Работодатель. Начать стажировку практиканта@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => true, 'context' => 'employer'],
			['title' => 'Выбор стажировки', 'active' => false, 'context' => 'internship'],
			['title' => 'Выбор графика стажировки', 'active' => false, 'context' => 'timetable'],
			['title' => 'Выбор практиканта', 'active' => false, 'context' => 'student'],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title">Выбор работодателя <br/>
			@if(isset($ids))
				<small>Отображаются только записи работодателей, доступные текущему пользователю</small>
			@endif
		</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="employers_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>ИНН</th>
						<th>Наименование</th>
						<th>Почтовый адрес</th>
						<th>Телефон</th>
						<th>Электронная почта</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Работодателей пока нет...</p>
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
				window.datatable = $('#employers_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					@if(isset($ids))
					ajax: '{!! route('e2s.start_internship.step1.data', ['ids' => $ids]) !!}',
					@else
					ajax: '{!! route('e2s.start_internship.step1.data') !!}',
					@endif
					responsive: true,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'inn', name: 'inn', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 2},
						{data: 'post_address', name: 'post_name', responsivePriority: 3},
						{data: 'phone', name: 'phone', responsivePriority: 3},
						{data: 'email', name: 'email', responsivePriority: 2},
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
