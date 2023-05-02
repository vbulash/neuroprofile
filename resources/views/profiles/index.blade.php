@extends('layouts.chain')

@section('service')
	Работа с описаниями результатов тестирования
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Тип описания',
		        'active' => false,
		        'context' => 'fmptype',
		        'link' => route('fmptypes.index'),
		    ],
		    ['title' => 'Нейропрофили', 'active' => true, 'context' => 'profile'],
		    ['title' => 'Блоки описания', 'active' => false, 'context' => 'block'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		@if ($codeCount == 0)
			Полный комплект нейропрофилей (все коды введены), добавление нового нейропрофиля невозможно
		@else
			<a href="{{ route('profiles.create') }}" class="btn btn-primary">Добавить
				нейропрофиль</a>
		@endif
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div class="table-responsive">
				<div class="table-responsive">
					<table class="table table-bordered table-hover text-nowrap" id="profiles_table" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 30px">#</th>
								<th>Код</th>
								<th>Наименование</th>
								<th>Количество блоков</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		@else
			<p>Нейропрофилей пока нет...</p>
		@endif
	</div>
	<form action="{{ route('profiles.export') }}" method="post" id="export">
		@csrf
		<input type="hidden" name="profile" id="profile" />
	</form>
@endsection

@if ($count > 0)
	@push('css_after')
		<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
	@endpush

	@push('js_after')
		<script src="{{ asset('js/datatables.js') }}"></script>
		<script>
			function clickExport(profile) {
				document.getElementById('profile').value = profile
				document.getElementById('export').submit()
			}

			document.getElementById('confirm-yes').addEventListener('click', (event) => {
				$.ajax({
					method: 'DELETE',
					url: "{{ route('profiles.destroy', ['profile' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить нейропрофиль &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function() {
				window.datatable = $('#profiles_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('profiles.index.data') !!}',
					responsive: true,
					pageLength: 25,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'code',
							name: 'code',
							responsivePriority: 1
						},
						{
							data: 'name',
							name: 'name',
							responsivePriority: 1,
							sortable: false
						},
						{
							data: 'fact',
							name: 'fact',
							responsivePriority: 2,
							sortable: false
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
