@extends('front.layouts.layout')

@push('title')
	- Предварительные проверки вход в тестирование
@endpush

@if (strlen($test->name) == 1)
	@section('testplace')
	@endsection
@else
	@push('testname')
		{{ $test->name }}
	@endpush
@endif

@section('content')
	<p>Обнаружены ошибки предварительной проверки:</p>
	@isset($message)
		<p>{!! $message !!}</p>
	@endisset
@endsection
