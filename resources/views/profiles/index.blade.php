@extends('layouts.chain')

@section('service')
    Работа с описаниями результатов тестирования
@endsection

@section('steps')
    @php
        $steps = [
            ['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index', ['sid' => session()->getId()])],
            ['title' => 'Нейропрофиль', 'active' => true, 'context' => 'profile'],
            ['title' => 'Блок описания', 'active' => false, 'context' => 'block'],
        ];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        @if ($codeCount == 0)
            Полный комплект нейропрофилей ({{ $count }}), добавление нового нейропрофиля невозможно
        @else
            <a href="{{ route('profiles.create', ['sid' => $sid]) }}" class="btn btn-primary mb-3">Добавить
                нейропрофиль</a>
        @endif
    </div>
    <div class="block-content p-4">
        @if ($count)
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-nowrap" id="profiles_table"
                           style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 30px">#</th>
                            <th>Код</th>
                            <th>Наименование</th>
                            <th>Количество блоков</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @else
            <p>Нейропрофилей пока нет...</p>
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
                    url: "{{ route('profiles.destroy', ['profile' => '0']) }}",
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
                document.getElementById('confirm-body').innerHTML = "Удалить нейропрофиль &laquo;" + name + "&raquo; ?";
                document.getElementById('confirm-yes').dataset.id = id;
                let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                confirmDialog.show();
            }

            $(function () {
                window.datatable = $('#profiles_table').DataTable({
                    language: {
                        "url": "{{ asset('lang/ru/datatables.json') }}"
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('profiles.index.data', ['sid' => session()->getId()]) !!}',
                    responsive: true,
                    pageLength: 25,
                    columns: [
                        {data: 'id', name: 'id', responsivePriority: 1},
                        {data: 'code', name: 'code', responsivePriority: 1},
                        {data: 'name', name: 'name', responsivePriority: 1, sortable: false},
                        {data: 'fact', name: 'fact', responsivePriority: 2, sortable: false},
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
