@extends('front.layouts.layout')

@push('title')
	- Завершение тестирования
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
	@php
		$texts = session('texts');
	@endphp

	@if (isset($texts) && isset($texts['posttext']))
		<p>{!! nl2br($texts['posttext']) !!}</p>
	@else
		<p>Спасибо за прохождение тестирования</p>
	@endif
	@auth
		<a href="{{ route('dashboard') }}" class="btn btn-primary mt-4">Возврат на главную страницу</a>
	@endauth
@endsection
