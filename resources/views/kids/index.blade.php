@extends('layouts.chain')

@section('service')
	Работа с ссылочными блоками
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Блок-предок', 'active' => false, 'context' => 'parent', 'link' => route('parents.index')],
			['title' => 'Блок-потомок', 'active' => true, 'context' => 'profile'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			Блоки-потомки (ссылочные блоки)
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
							<th>Тип описания</th>
							<th>Нейропрофиль</th>
							<th>Действия</th>
						</tr>
						</thead>
					</table>
				</div>
			</div>
		@else
			<p>Доступных блоков-потомков нет...</p>
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
			document.getElementById('confirm-yes').addEventListener('click', (event) => {
				$.ajax({
					method: 'GET',
					url: "{{ route('kids.unlink', ['kid' => '0']) }}",
					data: {
						id: event.target.dataset.id,
					},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}, false);

			function clickUnlink(id, name) {
				document.getElementById('confirm-title').innerText = "Подтвердите разрыв связи";
				document.getElementById('confirm-body').innerHTML = "Сделать блок &laquo;" + name + "&raquo; самостоятельным, не связанным с предком ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#blocks_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},

					processing: true,
					serverSide: true,
					ajax: '{!! route('kids.index.data') !!}',
					responsive: true,
					pageLength: 100,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 1},
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
