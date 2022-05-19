@extends('layouts.chain')

@section('service')
    Работа с клиентами и контрактами
@endsection

@section('steps')
    @php
        $steps = [
            ['title' => 'Клиент', 'active' => true, 'context' => 'client'],
            ['title' => 'Контракт', 'active' => false, 'context' => 'contract'],
            ['title' => 'Информация о контракте', 'active' => false, 'context' => 'info'],
        ];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        <a href="{{ route('clients.create', ['sid' => $sid]) }}" class="btn btn-primary mb-3">Добавить клиента</a>
    </div>
    <div class="block-content p-4">
        @if ($count)
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap" id="clients_table" style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="width: 30px">#</th>
                        <th>Наименование</th>
                        <th>Контракты</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                </table>
            </div>
        @else
            <p>Клиентов пока нет...</p>
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
                    url: "{{ route('clients.destroy', ['client' => '0']) }}",
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
                document.getElementById('confirm-body').innerHTML = "Удалить клиента &laquo;" + name + "&raquo; ?";
                document.getElementById('confirm-yes').dataset.id = id;
                let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                confirmDialog.show();
            }

            $(function () {
                window.datatable = $('#clients_table').DataTable({
                    language: {
                        "url": "{{ asset('lang/ru/datatables.json') }}"
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('clients.index.data') !!}',
                    responsive: true,
                    columns: [
                        {data: 'id', name: 'id', responsivePriority: 1},
                        {data: 'name', name: 'name', responsivePriority: 1},
                        {data: 'contracts', name: 'contracts', responsivePriority: 2, sortable: false},
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
