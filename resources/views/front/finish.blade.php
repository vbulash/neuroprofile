@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $test->name }}&raquo;
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
	<h1>Тестирование завершено<br />Результат записан в личном кабинете</h1>
	@auth
		<a href="{{ route('dashboard') }}" class="btn btn-primary mt-4">Возврат на главную страницу</a>
	@endauth
@endsection
