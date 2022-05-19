@extends('layouts.chain')

@section('service')
	Работа с описаниями результатов тестирования
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index', ['sid' => session()->getId()])],
			['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index', ['sid' => session()->getId()])],
			['title' => 'Блок описания', 'active' => true, 'context' => 'block', 'link' => route('blocks.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			Новый ссылочный блок описания<br/>
			<small>Выбор родительского блока, на который будет ссылаться новый ссылочный блок</small>
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
							<th>Количество блоков-потомков</th>
							<th>Тип описания</th>
							<th>Нейропрофиль</th>
							<th>Действия</th>
						</tr>
						</thead>
					</table>
				</div>
			</div>
		@else
			<p>Доступных родительских блоков нет...</p>
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
					ajax: '{!! route('aliases.index.data', ['sid' => session()->getId()]) !!}',
					responsive: true,
					pageLength: 100,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 1},
						{data: 'linked', name: 'linked', responsivePriority: 4},
						{data: 'fmptype', name: 'fmptype', responsivePriority: 2},
						{data: 'profile', name: 'profile', responsivePriority: 3},
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
