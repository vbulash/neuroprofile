@extends('layouts.chain')

@section('service')
    Работа с вопросами тестирования
@endsection

@section('steps')
    @php
        $steps = [
            ['title' => 'Набор вопросов', 'active' => true, 'context' => 'set'],
            ['title' => 'Вопросы', 'active' => false, 'context' => 'question'],
        ];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        <a href="{{ route('sets.create', ['sid' => $sid]) }}" class="btn btn-primary">Добавить набор вопросов</a>
    </div>
    <div class="block-content p-4">
        @if ($count)
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap" id="sets_table" style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="width: 30px">#</th>
                        <th>Наименование</th>
                        <th>Количество вопросов</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                </table>
            </div>
        @else
            <p>Наборов вопросов пока нет...</p>
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
                    url: "{{ route('sets.destroy', ['set' => '0']) }}",
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
                document.getElementById('confirm-body').innerHTML = "Удалить набор вопросов &laquo;" + name + "&raquo; ?";
                document.getElementById('confirm-yes').dataset.id = id;
                let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                confirmDialog.show();
            }

            $(function () {
                window.datatable = $('#sets_table').DataTable({
                    language: {
                        "url": "{{ asset('lang/ru/datatables.json') }}"
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('sets.index.data') !!}',
                    responsive: true,
                    columns: [
                        {data: 'id', name: 'id', responsivePriority: 1},
                        {data: 'name', name: 'name', responsivePriority: 1},
                        {data: 'questions', name: 'questions', responsivePriority: 2, sortable: false},
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
