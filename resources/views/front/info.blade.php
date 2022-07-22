@extends('front.layouts.layout')

@push('title') - Тест &laquo;{{ $test->name }}&raquo;@endpush

@push('testname') Тест &laquo;{{ $test->name }}&raquo;@endpush

@section('content')
    @if(session()->has('success'))
        <p>Успешная оплата результатов тестирования, письмо с результатами отправлено</p>
        @php(session()->forget('success'))
    @elseif(session()->has('error'))
        <p>Оплата не прошла. Полные результаты тестирования вы сможете получить, если еще раз зайдете в Робокассу по ссылке из письма с краткими результатами тестирования</p>
        <p>{{ session('error') }}</p>
        @php(session()->forget('error'))
    @elseif(session()->has('info'))
        <p>Успешная оплата результатов тестирования, письмо с результатами не отправлено.<br/>Обратитесь к администратору</p>
        @php(session()->forget('info'))
    @endif
@endsection
