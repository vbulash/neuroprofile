@extends('layouts.chain')

@section('service')
	Работа с клиентами и контрактами
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Клиент',
		        'active' => false,
		        'context' => 'client',
		        'link' => route('clients.index'),
		    ],
		    [
		        'title' => 'Аккаунт менеджеры',
		        'active' => true,
		        'context' => '',
		    ],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div class="d-flex flex-column">
			<h3 class="block-title fw-semibold mb-4">Аккаунт менеджеры</h3>
			<div class="d-flex">
				<a href="{{ route('clients.users.create', ['client' => $client->getKey()]) }}"
					class="btn btn-primary mb-4 me-4">Добавить
					нового
					аккаунт менеджера</a>
				<button class="btn btn-primary mb-4" id="add-admin" data-bs-toggle="modal" data-bs-target="#admins-list">Выбрать
					существующего аккаунт менеджера</button>
			</div>
			<small>Отсюда вы также можете перейти на <a href="{{ route('contracts.index') }}">Контракты текущего
					клиента</a></small>
		</div>
	</div>
	<div class="block-content p-4">
		<div class="table-responsive">
			<table class="table table-bordered table-hover text-nowrap" id="users_table" style="width: 100%;">
				<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>ФИО</th>
						<th>Электронная почта</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>

	<div class="modal fade" id="admins-list" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
		data-bs-keyboard="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Выбор существующего аккаунт менеджера</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				<div class="modal-body">
					<div class="mb-4">
						<select name="admins" class="select2 form-control" style="width:100%;" id="admins"></select>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">Закрыть</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="link-admin">Зафиксировать</button>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>
		function reloadSelect() {
			if (Object.keys(window.enabled).length === 0) {
				document.getElementById('add-admin').style.display = 'none';
			} else {
				document.getElementById('add-admin').style.display = 'block';
				let select = $('#admins');

				const data = [];
				for (let index in window.enabled)
					data.push({
						'id': index,
						'text': window.enabled[index],
					});
				data.sort((a, b) => {
					if (a.text === b.text) return 0;
					else if (a.text > b.text) return 1;
					else return -1;
				});
				select.empty().select2({
					language: 'ru',
					dropdownParent: $('#admins-list'),
					data: data,
				});
				//select.val('').trigger('change');
			}
		}

		document.getElementById('confirm-yes').addEventListener('click', (event) => {
			const id = event.target.dataset.user;
			const name = window.selected[id];

			$.ajax({
				method: 'POST',
				url: "{{ route('clients.users.detach') }}",
				data: {
					client: event.target.dataset.client,
					user: id,
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: () => {
					delete window.selected[id];
					window.enabled[id] = name;

					reloadSelect();
					window.datatable.ajax.reload();
				}
			});
		}, false);

		function clickDetach(client, user, name) {
			document.getElementById('confirm-title').innerText = "Подтвердите отмену привязки";
			document.getElementById('confirm-body').innerHTML =
				"Отменить привязку аккаунт менеджера &laquo;" + name +
				"&raquo; к клиенту ?<br/>" +
				"Физическое удаление аккаунт менеджера не произойдёт.";
			document.getElementById('confirm-yes').dataset.client = client;
			document.getElementById('confirm-yes').dataset.user = user;
			let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
			confirmDialog.show();
		}

		$(function() {
			window.datatable = $('#users_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('clients.users.index.data', ['client' => $client->getKey()]) !!}',
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

			window.enabled = {!! json_encode($enabled) !!};
			window.selected = {!! json_encode($selected) !!};
			reloadSelect();

			$('#link-admin').on('click', (event) => {
				const id = $('#admins').val();
				const name = window.enabled[id];

				$.ajax({
					method: 'POST',
					url: "{{ route('clients.users.attach') }}",
					data: {
						client: {{ $client->getKey() }},
						user: id,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						delete window.enabled[id];
						window.selected[id] = name;

						reloadSelect();
						window.datatable.ajax.reload();
					}
				});
			});
		});
	</script>
@endpush
