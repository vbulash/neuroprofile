@extends('front.layouts.layout')

@push('title')
	- Предварительные проверки вход в тестирование
@endpush

@push('testname')
	Тест &laquo;{{ $test->name }}&raquo;
@endpush

@section('content')
	<p>Обнаружены ошибки предварительной проверки:</p>
	<p>{!! $message !!}</p>
@endsection
