@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('testname')
	Тест &laquo;{{ $test->name }}&raquo;
@endpush

@section('content')
	<form role="form" method="get" action="{{ route('player.card', ['sid' => session()->getId()]) }}">
		@csrf
		<input type="hidden" value="{{ $sid}}" name="sid">
		<input type="hidden" name="nextblock" value="card">
		<div id="safari" style="display: none">
			<div id="mac" class="mb-4">
				<p>Мы обнаружили, что вы запускаете тест на компьютере Mac в браузере Safari. Для прохождения теста без ошибки
					вам необходимо переключить флажок, как показано на скриншоте ниже (на компьютере в меню Safari -> Настройки ->
					Конфиденциальность -> Отслеживание на веб-сайтах): </p>
				<img src="{{ asset('media/screenshots/mac-safari-cors.png') }}" alt="" class="img-fluid">
			</div>
			<div id="iphone" class="mb-4">
				<p>Мы обнаружили, что вы запускаете тест на iPhone в браузере Safari. Для прохождения теста без ошибки
					вам необходимо переключить флажок, как показано на скриншоте ниже (на телефоне Настройки -> Safari -> Без
					перекрестного отслеживания): </p>
				<img src="{{ asset('media/screenshots/iphone-safari-cors.jpg') }}" alt="" class="img-fluid">
			</div>
			<div class="checkbox mt-2 mb-4">
				<label>
					<input type="checkbox" id="safari-cors">
					Я исправил(а) настройки браузера Safari для успешного прохождения тестирования
				</label>
			</div>
		</div>
		<p>
			В нейротесте нет правильных и неправильных ответов. Каждый ответ правильный!<br />
			<strong>Время на вопрос - 5 секунд.</strong><br />
			Выберите изображение, которое привлекло Ваше внимание первым!<br />
			После нажатия на картинку ждите переход на следующий вопрос.<br />
			Если Вы пропустили какой-либо вопрос, он будет показан повторно.
		</p>

		@php
			$branding = session('branding');
			$label = ' ';
			if ($test->options & \App\Models\TestOptions::FACE_NEURAL->value) {
			    $label = 'Подготовка к тестированию: сделать снимок лица';
			} elseif ($test->options & \App\Models\TestOptions::AUTH_GUEST->value) {
			    $label = 'Начать тест';
			} elseif ($test->options & \App\Models\TestOptions::AUTH_FULL->value) {
			    $label = 'Перейти к анкете';
			} elseif ($test->options & \App\Models\TestOptions::AUTH_PKEY->value) {
			    $label = 'Ввести персональный ключ';
			} elseif ($test->options & \App\Models\TestOptions::AUTH_MIX->value) {
			    $label = 'Перейти к анкете и ввести персональный ключ';
			}
		@endphp

		<button type="submit" id="start"
			@if (isset($branding)) class="btn btn-lg" style="{{ $branding['buttonstyle'] }}"
			@else class="btn btn-primary btn-lg" @endif>{{ $label }}</button>
	</form>
@endsection

@push('scripts.injection')
	<script>
		function browserDetect() {

			let userAgent = navigator.userAgent;
			let browserName;

			if (userAgent.match(/chrome|chromium|crios/i)) {
				browserName = "chrome";
			} else if (userAgent.match(/firefox|fxios/i)) {
				browserName = "firefox";
			} else if (userAgent.match(/safari/i)) {
				browserName = "safari";
			} else if (userAgent.match(/opr\//i)) {
				browserName = "opera";
			} else if (userAgent.match(/edg/i)) {
				browserName = "edge";
			} else {
				browserName = "Модель браузера не определена";
			}

			return browserName;
		}

		function isIphone() {
			return navigator.userAgent.match(/iphone/i);
		}

		document.getElementById('safari-cors').addEventListener('change', event => {
			document.getElementById('start').disabled = !event.target.checked;
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			const start = document.getElementById('start');
			const safari = document.getElementById('safari');
			const mac = document.getElementById('mac');
			const iphone = document.getElementById('iphone');

			if (browserDetect() === 'safari') {
				safari.style.display = 'block';
				if (isIphone()) {
					mac.style.display = 'none';
					iphone.style.display = 'block';
				} else {
					mac.style.display = 'block';
					iphone.style.display = 'none';
				}
				start.disabled = true;
			} else {
				safari.style.display = 'none';
				start.disabled = false;
			}
		}, false);
	</script>
@endpush
