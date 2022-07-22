@extends('front.layouts.layout')

@push('title') - Тест &laquo;{{ $test->name }}&raquo;@endpush

@push('testname') Тест &laquo;{{ $test->name }}&raquo;@endpush

@push('step_description')
    Тестирование завершено
@endpush

@section('content')
    <h1>Тестирование завершено<br/>Результат записан в личном кабинете</h1>
@endsection

