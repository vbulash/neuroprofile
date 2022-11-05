@extends('layouts.chain')

@section('service')
    Работа с вопросами тестирования
@endsection

@section('steps')
    @php
        $steps = [
            ['title' => 'Набор вопросов', 'active' => false, 'context' => 'set', 'link' => route('sets.index')],
            ['title' => 'Вопросы', 'active' => true, 'context' => 'question', 'link' => route('questions.index')],
        ];
    @endphp
@endsection

@section('interior')
    <form role="form" class="p-5" method="post"
          id="client-create" name="client-create"
          action="{{ route('questions.store', ['sid' => session()->getId()]) }}"
          autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="block-header block-header-default">
            <h3 class="block-title fw-semibold">
                Создание вопроса для набора вопросов &laquo;{{ $set->name }}&raquo;.<br/>
                <small><span class="required">*</span> - поля, обязательные для заполнения</small>
            </h3>
        </div>
        <div class="block-content p-4">
            @php
                $fields = [
					['name' => 'kind', 'type' => 'hidden', 'value' => $kind],
					['name' => 'kindname', 'title' => 'Тип вопроса', 'required' => false, 'type' => 'text', 'value' => \App\Models\QuestionKind::getName(\App\Models\QuestionKind::SINGLE2->value), 'disabled' => true],
                    ['name' => 'learning', 'title' => 'Режим прохождения', 'required' => true, 'type' => 'select', 'options' => [
                        '0' => 'Реальный вопрос',
                        '1' => 'Учебный вопрос'
                    ]],
                    ['name' => 'timeout', 'title' => 'Таймаут прохождения вопроса, секунд', 'required' => true, 'type' => 'number', 'value' => 0],
					['name' => 'cue', 'title' => 'Отдельная подсказка к вопросу', 'required' => false, 'type' => 'text'],
                    //
                    ['name' => 'set_id', 'type' => 'hidden', 'value' => $set->getKey()],
                ];
            @endphp

            @foreach($fields as $field)
                @switch($field['type'])
                    @case('hidden')
                    @break

                    @default
                    <div class="row mb-4">
                        <label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
                            @if($field['required'])
                                <span class="required">*</span>
                            @endif</label>
                        @break
                        @endswitch

                        @switch($field['type'])

                            @case('text')
                            @case('email')
                            @case('number')
                            <div class="col-sm-5">
                                <input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @isset($field['disabled']) disabled @endisset
                                >
                            </div>
                            @break

                            @case('textarea')
                            <div class="col-sm-5">
						<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                  cols="30"
                                  rows="5"
						>{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}</textarea>
                            </div>
                            @break

                            @case('select')
                            <div class="col-sm-5">
                                <select class="form-control select2" name="{{ $field['name'] }}"
                                        id="{{ $field['name'] }}">
                                    @foreach($field['options'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break

                            @case('hidden')
                            <input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
                                   name="{{ $field['name'] }}" value="{{ $field['value'] }}">
                            @break
                        @endswitch
                        @switch($field['type'])
                            @case('hidden')
                            @break

                            @default
                    </div>
                    @break
                @endswitch
            @endforeach

            @php
                $fields = [
                    //
                    ['name' => 'image1', 'title' => 'Левая картинка вопроса', 'type' => 'image', 'required' => true],
                    ['name' => 'image2', 'title' => 'Правая картинка вопроса', 'type' => 'image', 'required' => true],
                    //
                    ['name' => 'value1', 'title' => 'Ключ левой картинки', 'required' => true, 'type' => 'select', 'options' => \App\Models\Question::$values],
                    ['name' => 'value2', 'title' => 'Ключ правой картинки', 'required' => true, 'type' => 'select', 'options' => \App\Models\Question::$values],
                    //
                ];
            @endphp

            <div class="row mb-4">
                @foreach($fields as $field)
                    <div class="col-sm-6">
                        <label class="col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
                            @if($field['required'])
                                <span class="required">*</span>
                            @endif
                        </label>
                        @switch($field['type'])
                            @case('text')
                            <input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
                                   name="{{ $field['name'] }}"
                                   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
                            >
                            @break;

                            @case('select')
                            <select class="form-control select2" name="{{ $field['name'] }}"
                                    id="{{ $field['name'] }}">
                                @foreach($field['options'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @break

                            @case('image')
                            <div class="row items-push mb-4">
                                <input type="file" class="form-control" id="{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       onchange="readImage(this)"
                                >
                            </div>
                            <div class="row mb-4" id="panel_{{ $field['name'] }}">
                                <div class="col-sm-9">
                                    <img id="preview_{{ $field['name'] }}"
                                         src=""
                                         alt=""
                                         class="image-preview">
                                </div>
                            </div>
                            @break
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>

        <div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
            <div class="row">
                <div class="col-sm-3 col-form-label">&nbsp;</div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a class="btn btn-secondary pl-3"
                       href="{{ route('questions.index', ['sid' => session()->getId()]) }}"
                       role="button">Закрыть</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js_after')
    <script>
        function readImage(input) {
            if (input.files && input.files[0]) {
                window.preview = 'preview_' + input.id;

                let reader = new FileReader();
                reader.onload = function (event) {
                    document.getElementById(window.preview).setAttribute('src', event.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }


        document.addEventListener("DOMContentLoaded", () => {
            //
        }, false);
    </script>
@endpush
