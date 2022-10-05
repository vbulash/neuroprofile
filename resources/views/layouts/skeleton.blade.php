<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

	<title>{!! env('APP_NAME') !!}</title>

	<meta name="description" content="{!! env('APP_NAME') !!}">
	<meta name="author" content="Valery Bulash">
	<meta name="robots" content="noindex, nofollow">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- Icons -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('media/favicons/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('media/favicons/favicon-16x16.png') }}">
	<link rel="manifest" href="{{ asset('media/favicons/site.webmanifest') }}">
	<link rel="mask-icon" href="{{ asset('media/favicons/safari-pinned-tab.svg') }}" color="#5bbad5">

	<!-- Fonts and Styles -->
	@stack('css_before')
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link
		href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
		rel="stylesheet">
	<link rel="stylesheet" id="css-main" href="{{ mix('css/app.css', '') }}">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
	@stack('css_after')

<!-- Scripts -->
	<script>
		window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
	</script>
</head>

<body @yield('body-params')>
@yield('body')

@include('layouts.partials.toast')
@include('layouts.partials.modal-confirm')
@include('tests.player-modal')

@stack('js_before')
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js"></script>
<script>

</script>
<script>
	function showToast(type, message, autohide) {
		let classList = "toast border-0 ";
		switch (type) {
			case 'error':
				classList = classList + 'bg-danger text-white';
				break;
			case 'success':
				classList = classList + 'bg-success text-white';
				break;
			case 'info':
			default:
				classList = classList + 'bg-primary text-white';
				break;
		}
		let elToast = document.getElementById('livetoast');
		let toast = new bootstrap.Toast(elToast);

		//toast.hide();

		elToast.className = classList;
		elToast.setAttribute('data-bs-autohide', autohide);

		let elToastBody = document.getElementById('lt-body');
		elToastBody.innerHTML = message;

		toast.show();
	}

	// Ошибки и сообщения
	// Broadcast
	@production
	@else
		Pusher.logToConsole = true;
	@endproduction

	let pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
		cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
	});

	let channel = pusher.subscribe('neuroprofile-channel-{!! $sid !!}');
	channel.bind('toast-event', (data) => {
		showToast(data.type, data.message, false);
	});

	document.addEventListener("DOMContentLoaded", () => {
		flatpickr('.flatpickr-input', {
			"locale": "ru"
		});

		@if (isset($errors) && $errors->any())
		@php
			session()->put('error', implode('<br/>', $errors->all()));
		@endphp
		@endif

		@if(session()->has('error'))
		showToast('error', '{!! session('error') !!}', false);
		@php
			session()->forget('error');
		@endphp
		@endif

		@if (session()->has('success'))
		showToast('success', '{!! session('success') !!}', true);
		@php
			session()->forget('success');
		@endphp
		@endif

		@if (session()->has('info'))
		showToast('info', '{!! session('info') !!}', true);
		@php
			session()->forget('info');
		@endphp
		@endif
	}, false);

	$(function () {
		try {
			$('.select2').select2({
				language: 'ru',
				theme: 'bootstrap-5'
			});
		} catch (e) {
			console.log(e.message);
		}
	});
</script>
@stack('js_after')

</body>

</html>

