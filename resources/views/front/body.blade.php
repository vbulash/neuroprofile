@extends('front.layouts.layout')

@push('title') - Тест &laquo;{{ $test->name }}&raquo;@endpush

@push('testname') Тест &laquo;{{ $test->name }}&raquo;@endpush

@push('step_description')
    Осталось секунд:
@endpush

@section('content')
    <form method="get" action="{{ route('player.body', ['question' => $step->id]) }}"
          enctype="multipart/form-data"
          name="step-form" id="step-form">
        @csrf
        <input type="hidden" name="sid" value="{{ $sid }}">
        <h4 class="mt-4 mb-4 text-center">Выберите одно изображение</h4>
        @php
            if(!isset($step)) return;

            $countdown = 0;
            $rows = 1;
            $columns = 2;
            if($step['quantity'] == \App\Models\QuestionSet::IMAGES2) {
                $rows = 1;
                $columns = 2;
            } elseif($step['quantity'] == \App\Models\QuestionSet::IMAGES4) {
                $rows = 2;
                $columns = 2;
            }
            $grid = intval(12 / $columns);
            $imageNo = 0;

            $letters = ['A', 'B', 'C', 'D'];
            $labels = ['А', 'Б', 'В', 'Г'];
            $qa = $step->toArray();
        @endphp
        @for ($row = 0; $row < $rows; $row++)
            <div class="row test-row mb-4">
                @for ($column = 0; $column < $columns; $column++)
                    <div class="col-{{ $grid }}">
                        <div class="@if($column == 0) mr-4 @endif">
                            <img id="image{{ $letters[$imageNo] }}"
                                 src="/uploads/{{ $qa['image' . $letters[$imageNo]] }}"
                                 data-id="{{ $letters[$imageNo] }}"
                                 alt=""
                                 class="step-image img-fluid">
                        </div>
                    </div>
                    @php($imageNo++)
                @endfor
            </div>
        @endfor
        <input type="hidden" name="answer" id="answer" value="0">
        <button type="submit" style="display:none;"></button>
    </form>

@endsection

@push('scripts.injection')
    <script>
        @php($countdown = 0)

        @if(intval($step->timeout) > 0)
        @php($countdown = $step->timeout)
        @elseif($step->learning == 1)
        @php($countdown = 10)
        @endif

        const form = document.getElementById('step-form');

        document.querySelectorAll(".step-image").forEach((input) => {
            input.addEventListener('click', event => {
                if (window.timer) clearInterval(window.timer);
                if (window.moves) {
                    clearInterval(window.moves);
                    // TODO Сбросить информацию в общую базу данных
                    localStorage.setItem('mousemoves', mouseMoves);
                }

                let image = event.target;
                document.getElementById('answer').value = image.dataset.id
                form.submit();
            }, false);
        });

        @if(intval($test->options) & \App\Models\TestOptions::MOUSE_TRACKING->value)
        let mousePos;
        let mouseMoves = [];

        document.addEventListener('mousemove', event => {
            let dot, eventDoc, doc, body, pageX, pageY;

            event = event || window.event; // IE-ism

            // If pageX/Y aren't available and clientX/Y are,
            // calculate pageX/Y - logic taken from jQuery.
            // (This is to support old IE)
            if (event.pageX == null && event.clientX != null) {
                eventDoc = (event.target && event.target.ownerDocument) || document;
                doc = eventDoc.documentElement;
                body = eventDoc.body;

                event.pageX = event.clientX +
                    (doc && doc.scrollLeft || body && body.scrollLeft || 0) -
                    (doc && doc.clientLeft || body && body.clientLeft || 0);
                event.pageY = event.clientY +
                    (doc && doc.scrollTop || body && body.scrollTop || 0) -
                    (doc && doc.clientTop || body && body.clientTop || 0);
            }

            let X = Math.min(event.pageX / window.innerWidth, 1);
            let Y = Math.min(event.pageY / window.innerHeight, 1);
            mousePos = {
                x: X,
                y: Y
            };
        }, false);
        @endif

        document.addEventListener("DOMContentLoaded", () => {
            @if(intval($test->options) & \App\Models\TestOptions::MOUSE_TRACKING->value)
                window.moves = setInterval(() => {
                let pos = mousePos;
                if (pos) {
                    let coords = JSON.stringify(pos);
                    mouseMoves.push(coords);
                }
            }, 100);
            @endif

            @if($countdown > 0)
            document.querySelectorAll('.step-countdown').forEach((counter) => {
                counter.innerText = {{ $countdown }};
            });
            window.counter = {{ $countdown }};

            window.timer = setInterval(() => {
                //console.log(window.counter);
                document.querySelectorAll('.step-countdown').forEach((counter) => {
                    counter.innerText = window.counter;
                });
                window.counter--;
                if (window.counter < 0) {
                    clearInterval(window.timer);
                    form.submit();
                }
            }, 1000);
            @endif
        }, false);
    </script>
@endpush
