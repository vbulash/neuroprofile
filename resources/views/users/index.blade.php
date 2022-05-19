@extends('layouts.chain')

@section('header') @endsection

@section('steps')
    @php
        $steps = [
            ['title' => 'Пользователи', 'active' => true, 'context' => 'user'],
        ];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        @hasrole('Администратор')
        <a href="{{ route('users.create', ['sid' => session()->getId()]) }}"
           class="btn btn-primary mt-3 mb-3">Добавить пользователя</a>
        @endhasrole
    </div>
    <div class="block-content p-4">
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
					url: "{{ route('users.destroy', ['user' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить пользователя &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
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
