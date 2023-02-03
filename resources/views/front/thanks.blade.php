@extends('front.layouts.layout')

@push('title') - Завершение тестирования @endpush

@push('testname') Тест &laquo;{{ $test->name }}&raquo;@endpush

@section('content')
    <p>Спасибо за прохождение тестирования</p>
	@auth
		<a href="{{ route('dashboard')}}" class="btn btn-primary mt-4">Возврат на главную страницу</a>
	@endauth
@endsection
