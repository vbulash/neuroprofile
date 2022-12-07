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
		<iframe src="https://faces.bulash.site?sid={{ session()->getId() }}&pkey={{ session('pkey') }}" width="80%" height="500px" frameborder="0" id="camera-frame"></iframe>
		<div class="ms-2 mb-4"><strong><span id="message">&nbsp;</span></strong></div>
		<button type="submit" class="btn btn-primary btn-lg" id="continue" disabled>Начать тестирование</button>
	</form>
@endsection

@push('scripts.injection')
    <script>
        let face_channel = pusher.subscribe('face-channel-{!! session()->getId() !!}');
		face_channel.bind('face-event', (data) => {
			let message = data.message.length == 0 ? "&nbsp;" : data.message;
			document.getElementById('message').innerHTML = "<span style='color:red'>" + message + "</span>";
		});

		let face_save_channel = pusher.subscribe('face-save-channel-{!! session()->getId() !!}');
		face_save_channel.bind('face-save-event', (image) => {
			document.getElementById('message').innerHTML = "<span style='color:green'>" + "Снимок сделан и сохранён" + "</span>";
			document.getElementById('continue').disabled = false;
		});

        document.addEventListener("DOMContentLoaded", () => {
        }, false);
    </script>
@endpush
