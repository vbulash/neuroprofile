@extends('layouts.chain')

@section('service')
    Работа с историей тестирования
@endsection

@section('steps')
    @php
        $steps = [['title' => 'История', 'active' => true, 'context' => 'history']];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        <div>
            <p><small>Эта таблица не помещается на экран по ширине. Для прокрутки влево / вправо вы можете пользоваться
                    полосой прокрутки ниже таблицы, а также клик мыши внутри таблицы и далее клавишами &larr; и
                    &rarr;</small></p>
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#filter-history"
                @if (!$count) disabled @endif>
                Экспорт истории тестирования
            </button>
        </div>
    </div>

    <div class="block-content p-4">
        @if ($count)
            <div>
                <table class="table table-bordered table-hover text-nowrap" id="history_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 30px">#</th>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Лицензия</th>
                            <th>Клиент</th>
                            <th>Контракт</th>
                            <th>Тест</th>
                            <th>Электронная почта</th>
                            <th>Коммерческий<br />контракт</th>
                            <th>Результат<br />оплачен</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @else
            <p>Истории тестирования пока нет...</p>
        @endif
    </div>

    <div class="modal fade" id="filter-history" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Фильтрация выгрузки истории тестирования по датам</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('history.export') }}" method="GET" id="form-export">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex flex-column align-items-start">
                            <small>
                                <p class="mb-2">Укажите граничные даты для выгрузки по следующим правилам:</p>
                                <ul>
                                    <li>Указаны обе даты - фильтрация в рамках дат</li>
                                    <li>Не указана дата &laquo;С&raquo; - все записи с начала ведения истории по дату
                                        &laquo;По&raquo;</li>
                                    <li>Не указана дата &laquo;По&raquo; - все записи позднее &laquo;С&raquo;</li>
                                    <li>Не указаны обе даты - нет фильтра = полная выгрузка. Просьба использовать данный
                                        режим с осторожностью - полная выборка формируется долго и могут быть проблемы в
                                        Excel с открытием большой таблицы</li>
                                </ul>
                                <p class="mb-4">Даты указываются включительно</p>
                            </small>
                            <div class="d-flex justify-content-between">
                                <div class="form-floating mb-4">
                                    <input type="text" class="flatpickr-input form-control" id="from" name="from"
                                        placeholder="Дата с" data-date-format="d.m.Y">
                                    <label for="from">Дата с</label>
                                </div>
                                <div class="form-floating ms-4 mb-4">
                                    <input type="text" class="flatpickr-input form-control" id="to" name="to"
                                        placeholder="Дата по" data-date-format="d.m.Y">
                                    <label for="from">Дата по</label>
                                </div>
                            </div>
                            <input type="hidden" name="field-list" id="field-list">
                            <label class="col-form-label mb-2" for="field-select">
                                Выберите поля, которые попадут в отчет. Пустой список = экспорт всех полей. Ctrl+клик = множественный выбор полей
                            </label>
                            <select class="form-control select2" id="field-select" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">
                            Закрыть
                        </button>

                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" data-role="submit"
                            id="btn-export">
                            Выполнить экспорт
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@if ($count > 0)
    @push('css_after')
        <link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @endpush

    @push('js_after')
        <script src="{{ asset('js/datatables.js') }}"></script>
        <script>
            function clickDelete(id) {
                $('#confirm-title').html('Подтвердите удаление');
                $('#confirm-body').html('Удалить запись истории тестирования № ' + id + ' ?');
                $('#confirm-yes').attr('data-id', id);
                $('#confirm-type').val('delete');
                let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                confirmDialog.show();
            }

            function clickMail(id) {
                $('#confirm-title').html('Подтвердите отправку письма');
                $('#confirm-body').html('Повторить письмо с результатами тестирования записи истории тестирования № ' + id +
                    ' ?');
                $('#confirm-yes').attr('data-id', id);
                $('#confirm-type').val('mail');
                let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                confirmDialog.show();
            }

            $(function() {
                window.datatable = $('#history_table').DataTable({
                    language: {
                        "url": "{{ asset('lang/ru/datatables.json') }}",
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('history.index.data') !!}',
                    //deferRender: true,
                    pageLength: 100,
                    scrollX: true,
                    sScrollXInner: "100%",
                    ordering: true,
                    order: [
                        [0, 'desc']
                    ],
                    searching: true,
                    columns: [{
                            data: 'id',
                            name: 'id',
                            responsivePriority: 1
                        },
                        {
                            data: 'date',
                            name: 'date',
                            responsivePriority: 2
                        },
                        {
                            data: 'time',
                            name: 'time',
                            responsivePriority: 2
                        },
                        {
                            data: 'license',
                            name: 'license',
                            responsivePriority: 2
                        },
                        {
                            data: 'client',
                            name: 'client',
                            responsivePriority: 3
                        },
                        {
                            data: 'contract',
                            name: 'contract',
                            responsivePriority: 3
                        },
                        {
                            data: 'test',
                            name: 'test',
                            responsivePriority: 2
                        },
                        {
                            data: 'email',
                            name: 'email',
                            responsivePriority: 2
                        },
                        {
                            data: 'commercial',
                            name: 'commercial',
                            responsivePriority: 4
                        },
                        {
                            data: 'paid',
                            name: 'paid',
                            responsivePriority: 2
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

                $('#confirm-yes').on('click', event => {
                    switch ($('#confirm-type').val()) {
                        case 'delete':
                            $.ajax({
                                method: 'DELETE',
                                url: "{{ route('history.destroy', ['history' => '0']) }}",
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
                        case 'mail':
                            $.ajax({
                                method: 'GET',
                                url: "{{ route('history.mail', ['sid' => $sid]) }}",
                                data: {
                                    history: event.target.dataset.id,
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: () => {
                                    window.location.reload();
                                }
                            });
                            break;
                    }
                });

                $('#field-select').on('select2:select', (event) => {
                    // let data = event.params.data;
                    // $('#btn-export').removeAttr('disabled');
                });

                $('#field-select').on('select2:unselect', (event) => {
                    // let data = event.params.data;
                    // if ($('#field-select').val().length === 0)
                    // 	$('#btn-export').attr('disabled', true);
                    // else
                    // 	$('#btn-export').removeAttr('disabled');
                });

                let select = $('#field-select');
                select.select2('destroy');
                select.select2({
                    language: 'ru',
                    dropdownParent: $('#filter-history'),
                    data: {!! $fields !!},
                    multiple: true,
                    placeholder: 'Выберите одно или несколько (c зажатой клавишей Ctrl) полей из выпадающего списка',
                });
                select.val(null).trigger('change');

                $('#form-export').on('submit', event => {
                    $('#field-list').val(JSON.stringify($('#field-select').val()));
                });
            });
        </script>
    @endpush
@endif
