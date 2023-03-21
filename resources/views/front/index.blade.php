@extends('front.layouts.layout')

@push('title')
	- Предварительные проверки вход в тестирование
@endpush

@push('testname')
	{{ $test->name }}
@endpush

@section('content')
	<p>Обнаружены ошибки предварительной проверки:</p>
	@isset($message)
		<p>{!! $message !!}</p>
	@endisset
@endsection
