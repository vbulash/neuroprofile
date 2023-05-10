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
		    ['title' => 'Нейропрофили', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index')],
		    ['title' => 'Блоки описания', 'active' => true, 'context' => 'block'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div class="d-flex align-items-center">
			<div class="dropdown me-2">
				<button class="btn btn-primary dropdown-toggle" type="button" id="blocks-create" data-bs-toggle="dropdown"
					aria-expanded="false">
					Добавить блок
				</button>
				<ul class="dropdown-menu" aria-labelledby="blocks-create">
					@php
						$buttons = [
						    [
						        'title' => \App\Models\BlockType::getName(\App\Models\BlockType::Text->value),
						        'action' => route('blocks.create', ['type' => \App\Models\BlockType::Text->value]),
						    ],
						    [
						        'title' => \App\Models\BlockType::getName(\App\Models\BlockType::Alias->value),
						        'action' => route('blocks.create', ['type' => \App\Models\BlockType::Alias->value]),
						    ],
						    [
						        'title' => \App\Models\BlockType::getName(\App\Models\BlockType::Image->value),
						        'action' => route('blocks.create', ['type' => \App\Models\BlockType::Image->value]),
						    ],
						    [
						        'title' => 'Клонирование существующего блока',
						        'action' => route('clones.index'),
						    ],
						];
					@endphp
					@foreach ($buttons as $button)
						<li><a class="dropdown-item" href="{{ $button['action'] }}">{{ $button['title'] }}</a></li>
					@endforeach
				</ul>
			</div>
			<div>В том числе и создать новый блок клонированием из существующего</div>
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
								<th>Название блока</th>
								<th>Показать название в результатах</th>
								<th>Тип блока</th>
								<th>&nbsp;</th>
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
					url: "{{ route('blocks.up') }}",
					data: {
						id: id,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}

			function clickDown(id) {
				$.post({
					url: "{{ route('blocks.down') }}",
					data: {
						id: id,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}

			document.getElementById('confirm-yes').addEventListener('click', (event) => {
				switch (document.getElementById('modal-confirm').dataset.kind) {
					case 'delete':
						$.ajax({
							method: 'DELETE',
							url: "{{ route('blocks.destroy', ['block' => '0']) }}",
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
					case 'unlink':
						$.ajax({
							method: 'GET',
							url: "{{ route('kids.unlink', ['kid' => '0']) }}",
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
				}


			}, false);

			function clickDelete(id, name) {
				document.getElementById('confirm-title').innerText = "Подтвердите удаление";
				document.getElementById('confirm-body').innerHTML = "Удалить блок &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				document.getElementById('modal-confirm').dataset.kind = 'delete';
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			function clickUnlink(id, name) {
				document.getElementById('confirm-title').innerText = "Подтвердите разрыв связи";
				document.getElementById('confirm-body').innerHTML = "Сделать блок &laquo;" + name +
					"&raquo; самостоятельным, не связанным с предком ?";
				document.getElementById('confirm-yes').dataset.id = id;
				document.getElementById('modal-confirm').dataset.kind = 'unlink';
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function() {
				window.datatable = $('#blocks_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('blocks.index.data') !!}',
					responsive: true,
					pageLength: 25,
					columns: [{
							data: 'sort_no',
							name: 'sort_no',
							responsivePriority: 1
						},
						{
							data: 'name',
							name: 'name',
							responsivePriority: 2,
							sortable: false
						},
						{
							data: 'show_title',
							name: 'show_title',
							responsivePriority: 3,
							sortable: false
						},
						{
							data: 'type',
							name: 'type',
							responsivePriority: 1
						},
						{
							data: 'action',
							name: 'action',
							sortable: false,
							responsivePriority: 1,
							className: 'd-flex no-wrap dt-actions'
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
