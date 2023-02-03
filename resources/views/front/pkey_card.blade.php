@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('testname')
	Тест &laquo;{{ $test->name }}&raquo;
@endpush

@section('content')
	<form method="get" action="{{ route('player.pkey', ['sid' => session()->getId()]) }}">
		@csrf
		<input type="hidden" value="{{ $sid }}" name="sid">
		<div class="form-group">
			<label for="pkey">Введите персональный ключ для начала тестирования</label>
			<input type="text" name="pkey" id="pkey"
				class="form-control col-lg-4 col-md-4 @error('pkey') is-invalid @enderror mt-2">
		</div>
		<button type="submit"
			@if (session('branding')) class="btn btn-lg mt-2" style="{{ session('buttonstyle') }}" @else class="btn btn-primary btn-lg mt-2" @endif>
			@if ($test->options & \App\Models\TestOptions::FACE_NEURAL->value)
				Подготовка к тестированию: сделать снимок лица
			@else
				Проверить и начать тестирование
			@endif
		</button>
	</form>
@endsection
