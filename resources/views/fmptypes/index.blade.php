@extends('layouts.chain')

@section('service')
    Работа с описаниями результатов тестирования
@endsection

@section('steps')
    @php
        $steps = [
            ['title' => 'Тип описания', 'active' => true, 'context' => 'fmptype'],
            ['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile'],
            ['title' => 'Блок описания', 'active' => false, 'context' => 'block'],
        ];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        <a href="{{ route('fmptypes.create', ['sid' => $sid]) }}" class="btn btn-primary mb-3">Добавить тип описания</a>
    </div>
    <div class="block-content p-4">
        @if ($count)
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-nowrap" id="fmptypes_table"
                           style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 30px">#</th>
                            <th>Наименование</th>
                            <th>Тип</th>
                            <th>Статус</th>
                            <th>Необходимо нейропрофилей</th>
                            <th>Фактически нейропрофилей</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @else
            <p>Типов описаний пока нет...</p>
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
                    url: "{{ route('fmptypes.destroy', ['fmptype' => '0']) }}",
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
                document.getElementById('confirm-body').innerHTML = "Удалить тип описания &laquo;" + name + "&raquo; ?";
                document.getElementById('confirm-yes').dataset.id = id;
                let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                confirmDialog.show();
            }

            $(function () {
                window.datatable = $('#fmptypes_table').DataTable({
                    language: {
                        "url": "{{ asset('lang/ru/datatables.json') }}"
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('fmptypes.index.data', ['sid' => session()->getId()]) !!}',
                    responsive: true,
                    columns: [
                        {data: 'id', name: 'id', responsivePriority: 1},
                        {data: 'name', name: 'name', responsivePriority: 1},
                        {data: 'cluster', name: 'cluster', responsivePriority: 2},
                        {data: 'active', name: 'active', responsivePriority: 2},
                        {data: 'limit', name: 'limit', responsivePriority: 3, sortable: false},
                        {data: 'fact', name: 'fact', responsivePriority: 3},
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
