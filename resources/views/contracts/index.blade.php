@extends('layouts.chain')

@section('service')
	Работа с клиентами и контрактами
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Клиент', 'active' => false, 'context' => 'client', 'link' => route('clients.index')], ['title' => 'Контракты', 'active' => true, 'context' => 'contract'], ['title' => 'Информация о контракте', 'active' => false, 'context' => 'info']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div class="d-flex flex-column">
			<div>
				<a href="{{ route('contracts.create') }}" class="btn btn-primary mb-4">Добавить контракт</a>
			</div>
			<small>Отсюда вы также можете перейти на <a
					href="{{ route('clients.users.index', ['client' => $client->getKey()]) }}">Администраторов текущего
					клиента</a></small>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="contracts_table" style="width: 100%;">
					<thead>
						<tr>
							<th>Номер контракта</th>
							<th>Дата начала</th>
							<th>Дата завершения</th>
							<th>Количество лицензий</th>
							<th>Статус</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Контрактов пока нет...</p>
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
					method: 'DELETE',
					url: "{{ route('contracts.destroy', ['contract' => '0']) }}",
					data: {
						id: event.target.dataset.id,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}, false);

			function clickDelete(id, name) {
				document.getElementById('confirm-title').innerText = "Подтвердите удаление";
				document.getElementById('confirm-body').innerHTML = "Удалить контракт № " + name + " ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function() {
				window.datatable = $('#contracts_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('contracts.index.data') !!}',
					responsive: true,
					columns: [{
							data: 'number',
							name: 'number',
							responsivePriority: 1
						},
						{
							data: 'start',
							name: 'start',
							responsivePriority: 2
						},
						{
							data: 'end',
							name: 'end',
							responsivePriority: 2
						},
						{
							data: 'license_count',
							name: 'license_count',
							responsivePriority: 4
						},
						{
							data: 'status',
							name: 'status',
							responsivePriority: 3
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

				window.datatable.on('draw', function() {
					$('.dropdown-toggle.actions').on('shown.bs.dropdown', (event) => {
						const menu = event.target.parentElement.querySelector('.dropdown-menu');
						let parent = menu.closest('.dataTables_wrapper');
						const parentRect = parent.getBoundingClientRect();
						parentRect.top = Math.abs(parentRect.top);
						const menuRect = menu.getBoundingClientRect();
						const buttonRect = event.target.getBoundingClientRect();
						const menuTop = Math.abs(buttonRect.top) + buttonRect.height + 4;
						if (menuTop + menuRect.height > parentRect.top + parentRect.height) {
							const clientHeight = parentRect.height + menuTop + menuRect.height - (
								parentRect.top + parentRect.height);
							parent.style.height = clientHeight.toString() + 'px';
						}
					});
				});
			});
		</script>
	@endpush
@endif
