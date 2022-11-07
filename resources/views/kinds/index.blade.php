@extends('layouts.chain')

@section('service')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Типы вопросов тестов', 'active' => true, 'context' => null]];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<a href="{{ route('kinds.create') }}" class="btn btn-primary mb-2">Добавить тип вопроса</a>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div class="table-responsive">
				<div class="table-responsive">
					<table class="table table-bordered table-hover text-nowrap" id="kinds_table" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 30px">#</th>
								<th>Наименование типа вопроса</th>
								<th>Количество изображений в вопросе</th>
								<th>Количество ответов в вопросе</th>
								<th>Подсказка к вопросам</th>
								<th>Действия</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		@else
			<p>Типов вопросов тестов пока нет...</p>
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
					url: "{{ route('kinds.destroy', ['kind' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить тип вопроса &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function() {
				window.datatable = $('#kinds_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('kinds.index.data') !!}',
					responsive: true,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'name',
							name: 'name',
							responsivePriority: 1
						},
						{
							data: 'images',
							name: 'images',
							responsivePriority: 2
						},
						{
							data: 'answers',
							name: 'answers',
							responsivePriority: 2
						},
						{
							data: 'cue',
							name: 'cue',
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
			});
		</script>
	@endpush
@endif
