@extends('layouts.wizard')

@section('service')Работа с описаниями результатов тестирования@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index', ['sid' => session()->getId()])],
			['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index', ['sid' => session()->getId()])],
			['title' => 'Блок описания', 'active' => true, 'context' => 'block'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div class="dropdown">
			<button class="btn btn-primary dropdown-toggle mb-3" type="button" id="blocks-create"
					data-bs-toggle="dropdown" aria-expanded="false">
				Добавить блок
			</button>
			<ul class="dropdown-menu" aria-labelledby="blocks-create">
				@php
					// TODO Учесть различие в action различных типов блоков по мере их реализации
					$buttons = [
                        [
                            'title' => \App\Models\BlockType::getName(\App\Models\BlockType::Text->value),
                            'action' => route('blocks.create', ['type' => \App\Models\BlockType::Text->value, 'sid' => $sid])
                        ],
                        [
                            'title' => \App\Models\BlockType::getName(\App\Models\BlockType::Alias->value),
                            'action' => route('aliases.index', ['sid' => $sid])
                        ],
					];
				@endphp
				@foreach($buttons as $button)
					<li><a class="dropdown-item" href="{{ $button['action'] }}">{{ $button['title'] }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count)
			<div class="table-responsive">
				<div class="table-responsive">
					<table class="table table-bordered table-hover text-nowrap" id="blocks_table" style="width: 100%;">
						<thead>
						<tr>
							<th>Номер по порядку</th>
							<th>Наименование блока</th>
							<th>Тип блока</th>
							<th>Действия</th>
						</tr>
						</thead>
					</table>
				</div>
			</div>
		@else
			<p>Блоков пока нет...</p>
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
					url: "{{ route('blocks.up', ['sid' => $sid]) }}",
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
					url: "{{ route('blocks.down', ['sid' => $sid]) }}",
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
					url: "{{ route('blocks.destroy', ['block' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить блока &laquo;" + name + "&raquo; ?";
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
					ajax: '{!! route('blocks.index.data', ['sid' => session()->getId()]) !!}',
					responsive: true,
					columns: [
						{data: 'sort_no', name: 'sort_no', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 2, sortable: false},
						{data: 'type', name: 'type', responsivePriority: 1},
						{data: 'action', name: 'action', sortable: false, responsivePriority: 1, className: 'no-wrap dt-actions'}
					]
				});
			});
		</script>
	@endpush
@endif
