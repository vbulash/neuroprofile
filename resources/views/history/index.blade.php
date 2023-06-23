@extends('layouts.chain')

@section('service')
	Работа с историей тестирования
@endsection

@section('steps')
	@php
		$steps = [['title' => 'История', 'active' => true, 'context' => 'history']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<p>Экспорт истории тестирования:</p>
			<form action="{{ route('history.export') }}" method="GET" id="form-export">
				@csrf
				<div class="d-flex flex-column align-items-start">
					<div class="d-flex justify-content-between">
						<div class="form-floating mb-4">
							<input type="text" class="flatpickr-input form-control" id="from" name="from" placeholder="Дата с"
								data-date-format="d.m.Y">
							<label for="from">Дата с</label>
						</div>
						<div class="form-floating ms-4 mb-4">
							<input type="text" class="flatpickr-input form-control" id="to" name="to" placeholder="Дата по"
								data-date-format="d.m.Y">
							<label for="from">Дата по</label>
						</div>
					</div>
					<input type="hidden" name="field-list" id="field-list">
					<label class="col-form-label mb-2" for="field-select">
						Выберите поля, которые попадут в отчет. Пустой список = экспорт всех полей. Ctrl+клик = множественный выбор полей
					</label>
					<select class="form-control select2" id="field-select" style="width: 100%;"></select>
					<button type="submit" class="btn btn-primary mt-4">Выполнить экспорт</button>
				</div>
			</form>
			<hr />
			<p><small>Эта таблица не помещается на экран по ширине. Для прокрутки влево / вправо вы можете пользоваться
					полосой прокрутки ниже таблицы, а также клик мыши внутри таблицы и далее клавишами &larr; и
					&rarr;</small></p>
		</div>
	</div>

	<div class="block-content p-4">
		@if ($count)
			<div>
				<table class="table table-bordered table-hover text-nowrap" id="history_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Дата</th>
							<th>Время</th>
							<th>Лицензия</th>
							<th>Клиент</th>
							<th>Контракт</th>
							<th>Тест</th>
							<th>Электронная почта</th>
							<th>Коммерческий<br />контракт</th>
							<th>Результат<br />оплачен</th>
							<th>Действия</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Истории тестирования пока нет...</p>
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
			function clickDelete(id) {
				$('#confirm-title').html('Подтвердите удаление');
				$('#confirm-body').html('Удалить запись истории тестирования № ' + id + ' ?');
				$('#confirm-yes').attr('data-id', id);
				$('#confirm-type').val('delete');
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			function clickMail(id) {
				$('#confirm-title').html('Подтвердите отправку письма');
				$('#confirm-body').html('Повторить письмо с результатами тестирования записи истории тестирования № ' + id +
					' ?');
				$('#confirm-yes').attr('data-id', id);
				$('#confirm-type').val('mail');
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function() {
				window.datatable = $('#history_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}",
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('history.index.data') !!}',
					//deferRender: true,
					pageLength: 100,
					scrollX: true,
					sScrollXInner: "100%",
					ordering: true,
					order: [
						[0, 'desc']
					],
					searching: true,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'date',
							name: 'date',
							responsivePriority: 2
						},
						{
							data: 'time',
							name: 'time',
							responsivePriority: 2
						},
						{
							data: 'license',
							name: 'license',
							responsivePriority: 2
						},
						{
							data: 'client',
							name: 'client',
							responsivePriority: 3
						},
						{
							data: 'contract',
							name: 'contract',
							responsivePriority: 3
						},
						{
							data: 'test',
							name: 'test',
							responsivePriority: 2
						},
						{
							data: 'email',
							name: 'email',
							responsivePriority: 2
						},
						{
							data: 'commercial',
							name: 'commercial',
							responsivePriority: 4
						},
						{
							data: 'paid',
							name: 'paid',
							responsivePriority: 2
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

				$('#confirm-yes').on('click', event => {
					switch ($('#confirm-type').val()) {
						case 'delete':
							$.ajax({
								method: 'DELETE',
								url: "{{ route('history.destroy', ['history' => '0']) }}",
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
							break;
						case 'mail':
							$.ajax({
								method: 'GET',
								url: "{{ route('history.mail') }}",
								data: {
									history: event.target.dataset.id,
								},
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
								success: () => {
									window.location.reload();
								}
							});
							break;
					}
				});

				$('#field-select').on('select2:select', (event) => {
					// let data = event.params.data;
					// $('#btn-export').removeAttr('disabled');
				});

				$('#field-select').on('select2:unselect', (event) => {
					// let data = event.params.data;
					// if ($('#field-select').val().length === 0)
					// 	$('#btn-export').attr('disabled', true);
					// else
					// 	$('#btn-export').removeAttr('disabled');
				});

				let select = $('#field-select');
				select.select2('destroy');
				select.select2({
					language: 'ru',
					data: {!! $fields !!},
					multiple: true,
					placeholder: 'Выберите одно или несколько (c зажатой клавишей Ctrl) полей из выпадающего списка',
				});
				select.val(null).trigger('change');

				$('#form-export').on('submit', event => {
					$('#field-list').val(JSON.stringify($('#field-select').val()));
				});
			});
		</script>
	@endpush
@endif
