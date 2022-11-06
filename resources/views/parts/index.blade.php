@extends('layouts.chain')

@section('service')
	Работа с вопросами тестирования
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Набор вопросов', 'active' => false, 'context' => 'set', 'link' => route('sets.index')], ['title' => 'Вопросы', 'active' => false, 'context' => 'question', 'link' => route('questions.index')], ['title' => 'Изображения вопросов', 'active' => true, 'context' => 'part']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		@if ($count < $question->kind->images)
		<div class="me-2">
			<a href="{{ route('parts.create') }}" class="btn btn-primary" type="button">
				Добавить изображение
			</a>
		</div>
		@else
			Достигнут лимит количества изображений в вопросе ({{ $question->kind->images }})
		@endif
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="parts_table" style="width: 100%;">
					<thead>
						<tr>
							<th>№ п/п</th>
							<th>Миниатюра изображения</th>
							<th>Ключ изображения</th>
							<th>Действия</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Изображений пока нет...</p>
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
					url: "{{ route('parts.destroy', ['part' => '0']) }}",
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

			function clickDelete(id) {
				document.getElementById('confirm-title').innerText = "Подтвердите удаление";
				document.getElementById('confirm-body').innerHTML = "Удалить изображения № " + id + " ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function() {
				window.datatable = $('#parts_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					ordering: false,
					order: [
						[1, 'asc']
					],
					processing: true,
					serverSide: true,
					ajax: '{!! route('parts.index.data') !!}',
					responsive: true,
					pageLength: 25,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'preview',
							name: 'preview',
							responsivePriority: 1,
							render: (data) => {
								if (data) {
									return "<img src=\"" + data + "\" alt=\"\" class=\"thumb-row\">\n";
								} else return '';
							}
						},
						{
							data: 'key',
							name: 'key',
							responsivePriority: 1
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
			});
		</script>
	@endpush
@endif
