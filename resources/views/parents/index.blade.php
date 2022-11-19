@extends('layouts.chain')

@section('service')
	Работа с ссылочными блоками
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Блок-предок', 'active' => true, 'context' => 'parent'],
			['title' => 'Блок-потомок', 'active' => false, 'context' => 'profile'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			Блоки-предки ссылочных блоков
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div class="table-responsive">
				<div class="table-responsive">
					<table class="table table-bordered table-hover text-nowrap" id="blocks_table" style="width: 100%;">
						<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Название блока</th>
							<th>Тип блока</th>
							<th>Тип описания</th>
							<th>Нейропрофиль</th>
							<th>Дата создания</th>
							<th>Дата изменения</th>
							<th>Действия</th>
						</tr>
						</thead>
					</table>
				</div>
			</div>
		@else
			<p>Доступных блоков-предков нет...</p>
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
				window.datatable = $('#blocks_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},

					processing: true,
					serverSide: true,
					ajax: '{!! route('parents.index.data') !!}',
					responsive: true,
					pageLength: 100,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 1},
						{data: 'type', name: 'type', responsivePriority: 2},
						{data: 'fmptype', name: 'fmptype', responsivePriority: 2},
						{data: 'profile', name: 'profile', responsivePriority: 3},
						{data: 'created_at', name: 'created_at', responsivePriority: 4},
						{data: 'updated_at', name: 'updated_at', responsivePriority: 4},
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
