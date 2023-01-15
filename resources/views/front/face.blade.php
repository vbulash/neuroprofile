@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('testname')
	Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('step_description')
	Снимок будет сделан через:
@endpush

@section('content')
	<div class="mt-4 mb-4" id="placeholder">
		<p>
			Лицо должно быть размещено ровно, полностью видно с достаточным освещением.<br />
			Если вы видите на экране зеленую сетку, значит лицо зафиксировано верно.<br />
			В ином случае следуйте инструкциям, расположенным на данном экране ниже.<br />
			Если зеленая сетка на лице зафиксируется в течение 5 секунд, то снимок лица выполнится автоматически и кнопка
			&laquo;Начать тестирование&raquo; станет доступной - вы сможете продолжить тестирование.
		</p>
		<p>
			<i class="fa-solid fa-camera"></i> Калибровка камеры...
		</p>
	</div>
	<form method="get" action="{{ route('player.body2') }}">
		@csrf
		<div class="d-flex flex-column">
			<div>
				<canvas class="output_canvas"></canvas>
			</div>
			<p id="message" class="mt-2 mb-2"></p>
			<div>
				<button type="submit" class="btn btn-primary btn-lg mt-4" id="continue" disabled>Начать тестирование</button>
			</div>
		</div>
		<video class="input_video" style="visibility: hidden;"></video>
		{{-- <video class="input_video"></video> --}}
	</form>
@endsection

@push('styles')
	<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
@endpush

@push('scripts.injection')
	<script type="inline-module" id="face_circle">
export default class FaceCircle {
	constructor (cx, cy, radius) {
		this.cx = parseInt(cx);
		this.cy = parseInt(cy);
		this.radius = parseInt(radius);
	}

	inFace(landmark) {
		return ((this.cx - this.cx * landmark.x * 2) ** 2 +
				(this.cy - this.cy * landmark.y * 2) ** 2) < this.radius ** 2;
	}

	landmarksInFace(landmarks) {
		for (let landmark of landmarks)
			if (!this.inFace(landmark))
				return false;
		return true;
	}
}
	</script>
	<script type="inline-module" id="face_angle">
export default class FaceAngle {
	constructor(canvasElement) {
		this.canvasElement = canvasElement;
	}

	xaxis(landmarks) {
		let diff = 0;
		let left = false;
		for (const points of [
			[6, 33, 263],	// Центральная точка переносицы / внешний угол левого глаза / внешний угол правого глаза
			[13, 61, 291],	// Центральная точка рта / левый угол рта / правый угол рта
			[1, 64, 294]	// Центральная точка носа / внешний угол левой ноздри / внешний угол правой ноздри
		]) {
			let x0 = landmarks[points[0]].x;		// Центральная точка для отсчета
			let x1 = x0 - landmarks[points[1]].x;	// Левая точка
			let x2 = landmarks[points[2]].x - x0;	// Правая точка
			left = left || (x1 < x2);
			diff = Math.max(diff, parseInt(Math.max(x1, x2) / (x1 + x2) * 100) - 50);
		}

		return diff > 10 ? 'Поверните голову ' + (left ? 'вправо' : 'влево') : '';
	}

	yaxis(landmarks) {
		let diff = 0;
		let up = false;

		const z0 = landmarks[33].z;
		const z1 = landmarks[263].z;
		if (Math.sign(z0) == Math.sign(z1)) {
			up = (Math.sign(z0) == 1);
			if (Math.abs(z0 * 100) > 2.5 || Math.abs(z1 * 100) > 2.5)
				return up ? 'Опустите голову вниз' : 'Поднимите голову вверх';
		}
		return '';
	}

	tilt(landmarks) {
		let diff = 0;
		let rightup = false;
		for (const points of [
			[33, 263],	// Внешний угол левого глаза / внешний угол правого глаза
			[61, 291],	// Левый угол рта / правый угол рта
			[64, 294]	// Внешний угол левой ноздри / внешний угол правой ноздри
		]) {
			let y1 = landmarks[points[0]].y * this.canvasElement.height;	// Левая точка
			let y2 = landmarks[points[1]].y * this.canvasElement.height;	// Правая точка
			rightup = rightup || (y1 > y2);
			diff = Math.max(diff, parseInt(Math.max(y1, y2) / (y1 + y2) * 100) - 50);
		}

		return diff > 3 ? 'Наклоните голову ' + (rightup ? 'вправо' : 'влево') : '';
	}
}
	</script>
	<script type="inline-module" id="face_distance">
export default class FaceDistance {
	constructor(canvasElement, bound) {
		this.canvasElement = canvasElement;
		this.bound = bound;
	}

	measure(landmarks) {
		let min = {
			x: 1,
			y: 1
		};
		let max = {
			x: 0,
			y: 0
		};
		landmarks.forEach((item) => {
			if (item.x < min.x) min.x = item.x;
			if (item.y < min.y) min.y = item.y;
			if (item.x > max.x) max.x = item.x;
			if (item.y > max.y) max.y = item.y;
		});
		min.x = parseInt(min.x * this.canvasElement.width);
		min.y = parseInt(min.y * this.canvasElement.height);
		max.x = parseInt(max.x * this.canvasElement.width);
		max.y = parseInt(max.y * this.canvasElement.height);
		if (max.x - min.x < this.bound * 0.7 || max.y - min.y < this.bound * 0.7)
			return 'Придвиньтесь ближе - лицо слишко далеко от камеры';
		return '';
	}
}
	</script>
	<script type="inline-module" id="face_illumination">
export default class FaceIllumination {
	constructor(context) {
		this.context = context;
	}

	estimate(landmarks) {
		let sum = 0;
		for (let landmark of landmarks) {
			const pixel = this.context.getImageData(landmark.x, landmark.y, 1, 1);
			sum += pixel.data[0] + pixel.data[1] + pixel.data[2];	// RGB, A игнорируем
		}
		const mean = parseInt(sum / (landmarks.length * 3));
		const OPTIMUM = 0.25;
		const min = parseInt(255 * (1 - OPTIMUM) / 2);
		const max = parseInt(min + 255 * OPTIMUM);
		if (mean < min)
			return 'Лицо недостаточно освещено - добавьте свет';
		else if (mean > max)
			return 'Слишком сильный источник света для лица - убавьте свет';
		else
			return '';
	}
}
	</script>
	<script src="{{ asset('js/inline-modules.js') }}" setup="false"></script>
	<script type="module">
		const FaceCircle = (await inlineImport('#face_circle')).default;
		const FaceAngle = (await inlineImport('#face_angle')).default;
		const FaceDistance = (await inlineImport('#face_distance')).default;
		const FaceIllumination = (await inlineImport('#face_illumination')).default;

		const videoElement = document.getElementsByClassName('input_video')[0];
		const canvasElement = document.getElementsByClassName('output_canvas')[0];
		const canvasCtx = canvasElement.getContext('2d', {willReadFrequently: true});
		let placeHolder = document.getElementById('placeholder');
		let stableIntervalId = null;
		let frozen = false;
		let saved = false;
		const COUNTDOWN = 5;
		let countdown = 0;

		function save(canvas) {
			const uuid = '{{ $pkey }}';
			const sex = 'M';
			const picture = canvas.toDataURL();

			// url: "{{ route('neural.shot.done') }}",
			const response = fetch("https://research.personahuman.ru/api/shot.done", {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify({
					uuid: uuid,
					sex: sex,
					photo: picture,
				})
			})
				.catch(error => console.log(error))
			.then((response) => {
				document.getElementById('continue').disabled = false;
			});
		}

		function onResults(results) {
			if (saved) return;

			if (placeHolder != null) {
				placeHolder.parentNode.removeChild(placeholder);
				placeHolder = null;
				const ratio = videoElement.videoWidth / videoElement.videoHeight;
				const parentNode = canvasElement.parentNode;
				if (videoElement.videoWidth > parentNode.clientWidth) {
					canvasElement.width = parentNode.clientWidth;
					canvasElement.height = parseInt(canvasElement.width / ratio);
				} else {
					canvasElement.width = videoElement.videoWidth;
					canvasElement.height = videoElement.videoHeight;
				}
			}

			let rect = {
				x: 0,
				y: 0,
				w: 0,
				h: 0
			}
			if (videoElement.videoWidth > videoElement.videoHeight) {	// Горизонтальные пропорции камеры (ноутбук / десктоп)
				rect.x = parseInt((videoElement.videoWidth - videoElement.videoHeight) / 2);
				rect.w = videoElement.videoHeight;
				rect.y = 0;
				rect.h = videoElement.videoHeight;

				// canvasElement.height = videoElement.videoHeight;
				// canvasElement.width = videoElement.videoWidth;
			}

			const fc = new FaceCircle(canvasElement.width / 2, canvasElement.height / 2, Math.min(canvasElement.width, canvasElement.height) * 0.45);
			canvasCtx.save();
			canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
			canvasCtx.drawImage(
				results.image, 0, 0, canvasElement.width, canvasElement.height);
			if (results.multiFaceLandmarks) {
				for (const landmarks of results.multiFaceLandmarks) {
					let messages = [];
					let message = '';
					let circle_color = '';
					let mesh_color = '';
					// Лицо помещается или не помещается в круг
					if (fc.landmarksInFace(landmarks)) {
						circle_color = '#00FF00';
						mesh_color = '#00FF00';
					} else {
						circle_color = '#FF0000';
						mesh_color = '#FF0000';
						messages.push('Поместите лицо в окружность');
					}
					// Лицо далеко / близко
					const fd = new FaceDistance(canvasElement, fc.radius);
					message = fd.measure(landmarks);
					if (message) {
						mesh_color = '#FF0000';
						messages.push(message);
					}
					// Смещения / повороты лица по 3 осям
					const fa = new FaceAngle(canvasElement);
					// горизонтальное смещение
					message = fa.xaxis(landmarks);
					if (message) {
						mesh_color = '#FF0000';
						messages.push(message);
					}
					// вертикальное смещение
					message = fa.yaxis(landmarks);
					if (message) {
						mesh_color = '#FF0000';
						messages.push(message);
					}
					// Повороты головы влево и вправо
					message = fa.tilt(landmarks);
					if (message) {
						mesh_color = '#FF0000';
						messages.push(message);
					}
					// Освещение
					const fi = new FaceIllumination(canvasCtx);
					message = fi.estimate(landmarks);
					if (message) {
						mesh_color = '#FF0000';
						messages.push(message);
					}

					//
					canvasCtx.beginPath();
					canvasCtx.arc(fc.cx, fc.cy, fc.radius, 0, 2 * Math.PI);
					canvasCtx.strokeStyle = circle_color;
					canvasCtx.stroke();

					drawConnectors(canvasCtx, landmarks, FACEMESH_TESSELATION,
						{color: mesh_color + '20', lineWidth: 1});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_RIGHT_EYE, {color: '#FF3030'});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_RIGHT_EYEBROW, {color: '#FF3030'});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_RIGHT_IRIS, {color: '#FF3030'});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_LEFT_EYE, {color: '#30FF30'});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_LEFT_EYEBROW, {color: '#30FF30'});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_LEFT_IRIS, {color: '#30FF30'});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_FACE_OVAL, {color: '#E0E0E0'});
					// drawConnectors(canvasCtx, landmarks, FACEMESH_LIPS, {color: '#E0E0E0'});

					if (messages.length > 0) {
						clearInterval(stableIntervalId);
						stableIntervalId = null;
						frozen = false;
						document.querySelectorAll('.step-countdown').forEach((counter) => {
							counter.innerText = 'нет';
						});
						document.getElementById('message').innerHTML = messages.join('<br/>');
					} else if (frozen) {	// Идеальная картинка, только что сработал таймер
						saved = true;
						clearInterval(stableIntervalId);
						stableIntervalId = null;
						frozen = false;
						save(results.image);	// Без AR-элементов
						document.getElementById('message').innerHTML = 'Снимок сделан и сохранён';
						document.querySelectorAll('.step-countdown').forEach((counter) => {
							counter.innerText = '-';
						});
					} else {	// Идеальная картинка, запускаем 3-секундный таймер
						document.getElementById('message').innerHTML = '';
						if (stableIntervalId == null) {
							countdown = COUNTDOWN;
							stableIntervalId = setInterval(() => {
								document.querySelectorAll('.step-countdown').forEach((counter) => {
									counter.innerText = countdown;
								});
								document.getElementById('message').innerHTML = 'Снимок будет сделан через: ' + countdown.toString();
								if (countdown-- <= 0) {
									clearInterval(stableIntervalId);
									frozen = true;
								}
							}, 1000);
						}
					}
				}
			}
			canvasCtx.restore();
		}

		const faceMesh = new FaceMesh({
			locateFile: (file) => {
				return `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`;
			}
		});
		faceMesh.setOptions({
			selfieMode: true,
			maxNumFaces: 1,
			refineLandmarks: true,
			minDetectionConfidence: 0.5,
			minTrackingConfidence: 0.5
		});
		faceMesh.onResults(onResults);

		const camera = new Camera(videoElement, {
			onFrame: async () => {
				await faceMesh.send({image: videoElement});
			},
			// width: 1280,
			// height: 720,
			// width: Math.min(1280, window.innerWidth),
		});
		camera.start();

		document.addEventListener("DOMContentLoaded", () => {

		}, false);
	</script>
@endpush
