@extends('layouts.chain')

@section('service')
    Работа с клиентами и контрактами
@endsection

@section('steps')
    @php
        $steps = [
            ['title' => 'Клиент', 'active' => false, 'context' => 'client', 'link' => route('clients.index', ['sid' => session()->getId()])],
            ['title' => 'Контракт', 'active' => true, 'context' => 'contract'],
            ['title' => 'Информация о контракте', 'active' => false, 'context' => 'info'],
        ];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        <a href="{{ route('contracts.create', ['sid' => $sid]) }}" class="btn btn-primary mb-3">Добавить контракт</a>
    </div>
    <div class="block-content p-4">
        @if ($count)
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap" id="contracts_table"
                       style="width: 100%;">
                    <thead>
                    <tr>
                        <th>Номер контракта</th>
                        <th>Дата начала</th>
                        <th>Дата завершения</th>
                        <th>Количество лицензий</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                </table>
            </div>
        @else
            <p>Контрактов пока нет...</p>
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
                    url: "{{ route('contracts.destroy', ['contract' => '0']) }}",
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
                document.getElementById('confirm-body').innerHTML = "Удалить контракт № " + name + " ?";
                document.getElementById('confirm-yes').dataset.id = id;
                let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                confirmDialog.show();
            }

            $(function () {
                window.datatable = $('#contracts_table').DataTable({
                    language: {
                        "url": "{{ asset('lang/ru/datatables.json') }}"
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('contracts.index.data') !!}',
                    responsive: true,
                    columns: [
                        {data: 'number', name: 'number', responsivePriority: 1},
                        {data: 'start', name: 'start', responsivePriority: 2},
                        {data: 'end', name: 'end', responsivePriority: 2},
                        {data: 'license_count', name: 'license_count', responsivePriority: 4},
                        {data: 'status', name: 'status', responsivePriority: 3},
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
