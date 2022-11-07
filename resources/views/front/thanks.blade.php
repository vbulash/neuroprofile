@extends('front.layouts.layout')

@push('title') - Завершение тестирования @endpush

@push('testname') Тест &laquo;{{ $test->name }}&raquo;@endpush

@section('content')
    <p>Спасибо за прохождение тестирования</p>
@endsection
