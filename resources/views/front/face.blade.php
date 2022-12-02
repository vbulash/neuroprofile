@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('testname')
	Тест &laquo;{{ $test->name }}&raquo;
@endpush

@section('content')
	<div class="mt-4 mb-4">
		Лицо должно быть размещено ровно, полностью видно с достаточным освещением.<br />
		Если вы видите на экране зеленую сетку, значит лицо зафиксировано верно.<br />
		В ином случае следуйте инструкциям, расположенным на данном экране ниже.<br />
		Если зеленая сетка на лице зафиксируется в течение 3 секунд, то снимок лица выполнится автоматически и кнопка
		&laquo;Начать тестирование&raquo; станет доступной - вы сможете продолжить тестирование.
	</div>
	<form method="get" action="{{ route('player.body2') }}">
		@csrf
		<iframe src="https://faces.bulash.site" width="80%" height="500px" frameborder="0" id="camera-frame"></iframe>
		<p class="mb-4"><strong><span id="message">Область сообщений</span></strong></p>
		<button type="submit" class="btn btn-primary btn-lg" id="continue" disabled>Начать тестирование</button>
	</form>
@endsection
