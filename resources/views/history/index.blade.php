@extends('layouts.chain')

@section('service')
	Работа с историей тестирования
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'История', 'active' => true, 'context' => 'history'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<small>Эта таблица не помещается на экран по ширине. Для прокрутки влево / вправо вы можете пользоваться
				полосой прокрутки ниже таблицы, а также клик мыши внутри таблицы и далее клавишами &larr; и &rarr;</small>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div>
				<table class="table table-bordered table-hover text-nowrap" id="history_table" style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Прохождение</th>
						<th>Лицензия</th>
						<th>Клиент</th>
						<th>Контракт</th>
						<th>Тест</th>
						<th>Электронная почта</th>
						<th>Коммерческий<br/>контракт</th>
						<th>Результат<br/>оплачен</th>
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
				$('#confirm-body').html('Повторить письмо с результатами тестирования записи истории тестирования № ' + id + ' ?');
				$('#confirm-yes').attr('data-id', id);
				$('#confirm-type').val('mail');
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#history_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}",
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('history.index.data') !!}',
					deferRender: true,
					pageLength: 100,
					scrollX: true,
					sScrollXInner: "100%",
					ordering: true,
					order: [[0, 'desc']],
					searching: true,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'timestamp', name: 'timestamp', responsivePriority: 2},
						{data: 'license', name: 'license', responsivePriority: 2},
						{data: 'client', name: 'client', responsivePriority: 3},
						{data: 'contract', name: 'contract', responsivePriority: 3},
						{data: 'test', name: 'test', responsivePriority: 2},
						{data: 'email', name: 'email', responsivePriority: 2},
						{data: 'commercial', name: 'commercial', responsivePriority: 4},
						{data: 'paid', name: 'paid', responsivePriority: 2},
						{
							data: 'action',
							name: 'action',
							sortable: false,
							responsivePriority: 1,
							className: 'no-wrap dt-actions'
						}
					]
				});

				$('#confirm-yes').on('click', event => {
					switch($('#confirm-type').val()) {
						case 'delete':
							$.ajax({
								method: 'DELETE',
								url: "{{ route('history.destroy', ['history' => '0']) }}",
								data: {
									id: event.target.dataset.id,
								},
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
								success: () => {
									window.datatable.ajax.reload();
								}
							});
							break;
						case 'mail':
							$.ajax({
								method: 'GET',
								url: "{{ route('history.mail', ['sid' => $sid]) }}",
								data: {
									history: event.target.dataset.id,
								},
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
								success: () => {
									window.location.reload();
								}
							});
							break;
					}
				});
			});
		</script>
	@endpush
@endif
