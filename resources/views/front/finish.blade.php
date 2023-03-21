@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('testname')
	{{ $test->name }}
@endpush

@push('step_description')
	Тестирование завершено
@endpush

@section('content')
	<h1>Тестирование завершено<br />Результат записан в личном кабинете</h1>
	@auth
		<a href="{{ route('dashboard') }}" class="btn btn-primary mt-4">Возврат на главную страницу</a>
	@endauth
@endsection
