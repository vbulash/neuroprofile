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
		{{-- <iframe src="https://faces.bulash.site?sid={{ session()->getId() }}&pkey={{ session('pkey') }}" width="80%" height="500px" frameborder="0" id="camera-frame"></iframe>
		<div class="ms-2 mb-4"><strong><span id="message">&nbsp;</span></strong></div>
		<button type="submit" class="btn btn-primary btn-lg" id="continue" disabled>Начать тестирование</button> --}}
		<div class="d-flex flex-column">
			<img src="" alt="Калибровка камеры..." id='image' class='img-fluid mb-4' style="max-width: 480;">
			<div>
				<button type="submit" class="btn btn-primary btn-lg" id="continue">Сделать снимок</button>
			</div>
			<div>
				<video id="webcam" autoplay playsinline></video>
			</div>
			<canvas id="canvas" class="mb-4"></canvas>
		</div>
	</form>
@endsection

@push('scripts.injection')
	<script src="{{ asset('js/webcam-easy.min.js') }}"></script>
	<script>
		// let face_channel = pusher.subscribe('face-channel-{!! session()->getId() !!}');
		// face_channel.bind('face-event', (data) => {
		// 	let message = data.message.length == 0 ? "&nbsp;" : data.message;
		// 	document.getElementById('message').innerHTML = "<span style='color:red'>" + message + "</span>";
		// });

		// let face_save_channel = pusher.subscribe('face-save-channel-{!! session()->getId() !!}');
		// face_save_channel.bind('face-save-event', (image) => {
		// 	document.getElementById('message').innerHTML = "<span style='color:green'>" + "Снимок сделан и сохранён" +
		// 		"</span>";
		// 	document.getElementById('continue').disabled = false;
		// });
		let webCamElement = document.getElementById("webcam");
		let canvasElement = document.getElementById("canvas");
		let context = canvasElement.getContext('2d');
		let image = document.getElementById('image');
		let ratio = 1;
		let webcam = null;
		let timerId = null;
		const CROP = 480;

		function syncDimensions() {
			if (ratio == 1) return;

			webCamElement.width =
				canvasElement.width =
				image.width = Math.min(CROP, image.parentElement.clientWidth);
			webCamElement.height =
				canvasElement.height =
				image.height = image.width * ratio;
		}

		window.onresize = (event) => {
			syncDimensions();
		};

		webCamElement.onplaying = function() {
			ratio = webCamElement.videoHeight / webCamElement.videoWidth;
		}

		function showFrame() {
			if (ratio == 1) return;

			syncDimensions();
			// context.drawImage(webCamElement, 0, 0, webCamElement.width, webCamElement.height);
			let picture = webcam.snap();

			// TODO обработать исходную картинку, наложить AR-элементы, вернуть как base64 в picture

			image.src = picture;
		}

		document.getElementById('continue').onclick = () => {
			let picture = webcam.snap();
			// console.log(picture);
			syncDimensions();
			let sex = 'M';

			let response = fetch("{{ route('neural.shot.done') }}", {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
					body: JSON.stringify({
						uuid: "{{ $pkey }}",
						sex: sex,
						photo: picture,
					})
				})
				.catch(error => console.log(error));
		};

		document.addEventListener("DOMContentLoaded", () => {
			webcam = new Webcam(webCamElement, 'user', canvasElement, null);
			webCamElement.style.visibility = 'hidden';
			canvasElement.style.visibility = 'hidden';
			webcam.start();
			// window.dispatchEvent(new Event('resize'));
			timerId = setInterval(showFrame, 0);
		}, false);
	</script>
@endpush
