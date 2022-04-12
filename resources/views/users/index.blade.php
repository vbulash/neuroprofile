@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Пользователи</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Настройки</li>
						<li class="breadcrumb-item active" aria-current="page">Пользователи</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<!-- Table -->
		<div class="block block-rounded">
			<div class="block-header block-header-default">
				@can('users.create')
					<a href="{{ route('users.create', ['sid' => session()->getId()]) }}" class="btn btn-primary mt-3 mb-3">Добавить пользователя</a>
				@endcan
			</div>
			<div class="block-content pb-3">
				@if ($count > 0)
					<div class="table-responsive">
						<table class="table table-bordered table-hover text-nowrap" id="users_table"
							   style="width: 100%;">
							<thead>
							<tr>
								<th style="width: 30px">#</th>
								<th>ФИО</th>
								<th>Электронная почта</th>
								<th>Действия</th>
							</tr>
							</thead>
						</table>
					</div>
				@else
					<p>Пользователей пока нет...</p>
				@endif
			</div>
		</div>
		<!-- END Table -->
	</div>

@endsection

@if ($count > 0)
@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>
		function clickDelete(id, name) {
			if(window.confirm('Удалить пользователя "' + name + '" ?')) {
				$.ajax({
					method: 'DELETE',
					url: "{{ route('users.destroy', ['user' => '0']) }}",
					data: {
						id: id,
					},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}
		}

		$(function () {
			window.datatable = $('#users_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('users.index.data') !!}',
				responsive: true,
				columns: [
					{data: 'id', name: 'id', responsivePriority: 1},
					{data: 'name', name: 'name', responsivePriority: 2},
					{data: 'email', name: 'email', responsivePriority: 3},
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
