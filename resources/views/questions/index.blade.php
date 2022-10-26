@extends('layouts.chain')

@section('service')
	Работа с вопросами тестирования
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Набор вопросов', 'active' => false, 'context' => 'set', 'link' => route('sets.index', ['sid' => session()->getId()])],
			['title' => 'Вопросы', 'active' => true, 'context' => 'question'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<a href="{{ route('questions.create', ['sid' => $sid]) }}" class="btn btn-primary">Добавить вопрос</a>
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="questions_table" style="width: 100%;">
					<thead>
					<tr>
						<th>Номер по порядку</th>
						<th>Миниатюры изображений</th>
						<th>Режим прохождения</th>
						<th>Таймаут, секунд</th>
						<th>Ключи вопроса</th>
						<th>Подсказка к вопросу</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Вопросов пока нет...</p>
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
			function clickUp(id) {
				$.post({
					url: "{{ route('questions.up', ['sid' => $sid]) }}",
					data: {
						id: id,
					},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}

			function clickDown(id) {
				$.post({
					url: "{{ route('questions.down', ['sid' => $sid]) }}",
					data: {
						id: id,
					},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}

			document.getElementById('confirm-yes').addEventListener('click', (event) => {
				$.ajax({
					method: 'DELETE',
					url: "{{ route('questions.destroy', ['question' => '0']) }}",
					data: {
						id: event.target.dataset.id,
					},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}, false);

			function clickDelete(id, name) {
				document.getElementById('confirm-title').innerText = "Подтвердите удаление";
				document.getElementById('confirm-body').innerHTML = "Удалить вопроса № " + name + " ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#questions_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					ordering: false,
					order: [[1, 'asc']],
					processing: true,
					serverSide: true,
					ajax: '{!! route('questions.index.data') !!}',
					responsive: true,
					pageLength: 100,
					columns: [
						{data: 'sort_no', name: 'sort_no', responsivePriority: 1},
						{
							data: 'preview', name: 'preview', responsivePriority: 3, render: (data) => {
								if (data) {
									let thumbs = JSON.parse(data.replace(/&quot;/g, '"'));
									let preview = '';
									thumbs.forEach((thumb) => {
										preview = preview +
											"<img src=\"" + thumb + "\" alt=\"\" class=\"thumb-row\">\n";
									});
									return preview;
								} else return '';
							}
						},
						{data: 'learning', name: 'learning', responsivePriority: 2},
						{data: 'timeout', name: 'timeout', responsivePriority: 2},
						{data: 'key', name: 'key', responsivePriority: 3},
						{data: 'cue', name: 'cue', responsivePriority: 3, render: (data) => {
							return data ? data : 'Нет';
						}},
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
