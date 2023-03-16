@extends('layouts.chain')

@section('header')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Администраторы клиентов', 'active' => true, 'context' => 'admin']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div class="d-flex flex-column">
			<h3 class="block-title fw-semibold mb-4">Администраторы клиентов</h3>
			<a href="{{ route('adminclients.create') }}" class="btn btn-primary">Добавить администратора клиента</a>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="admins_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>ФИО</th>
							<th>Электронная почта</th>
							<th>Управляет клиентами</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Администраторов клиентов пока нет...</p>
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
					url: "{{ route('adminclients.destroy', ['adminclient' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить администратора клиента &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function() {
				window.datatable = $('#admins_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('adminclients.index.data') !!}',
					responsive: true,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'name',
							name: 'name',
							responsivePriority: 2
						},
						{
							data: 'email',
							name: 'email',
							responsivePriority: 3
						},
						{
							data: 'clients',
							name: 'clients',
							responsivePriority: 3,
							render: (data) => {
								if (data) {
									let clients = JSON.parse(data.replace(/&quot;/g, '"'));
									return clients.join("<br/>");
								} else return '';
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
