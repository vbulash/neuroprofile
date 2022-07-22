@extends('front.layouts.layout')

@push('title') - Тест &laquo;{{ $test->name }}&raquo;@endpush

@push('testname') Тест &laquo;{{ $test->name }}&raquo;@endpush

@push('step_description')
    Расчет результатов тестирования
@endpush

@section('content')
    <h1>Выполняется расчет результатов тестирования...</h1>
    <form method="get" id="calc-form"
          action="{{ route('player.calculate', ['history_id' => $history_id]) }}">
        @csrf
        <input type="hidden" name="sid" value="{{ $sid }}">
        <button type="submit" class="btn btn-primary" style="display:none;"></button>
    </form>
@endsection

@push('scripts.injection')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById('calc-form');
            form.submit();
        }, false);
    </script>
@endpush
