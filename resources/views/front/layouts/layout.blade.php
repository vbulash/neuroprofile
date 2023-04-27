<!doctype html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ env('APP_NAME') }}@stack('title')</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	{{--    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> --}}
	<link rel="stylesheet" href="{{ asset('assets/front/css/front.css') }}">
	{{-- <script src="https://cdn.tailwindcss.com"></script> --}}
	{{--    https://realfavicongenerator.net/ --}}
	<!-- favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">
	<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

	<!-- Page styles -->
	@stack('styles')
</head>

@section('content_full')
	<div class="col-md-8">
		@yield('content')
	</div>
@endsection

<body>
	<div class="container-fluid main-header g-0">
		@php
			$branding = session('branding');
			//dd($branding);
		@endphp
		<nav class="navbar navbar-dark bg-primary d-lg-flex p-1"
			@if (isset($branding)) style="{{ $branding['navstyle'] }}" @endif>
			<div class="navbar-brand" @if (isset($branding)) style="{{ $branding['textstyle'] }}" @endif>
				{{--            <a href="{{ route('admin.index') }}"> --}}
				@if (isset($branding) && isset($branding['logo']))
					<span id="preview_logo">
						<img src="{{ '/uploads/' . $branding['logo'] }}" class="preview_logo"
							style="height: 56px; margin-right: 1.25rem;">
					</span>
				@else
					<i class="fas fa-home"></i>
				@endif
				{{ isset($branding) ? $branding['company'] : env('APP_NAME') }}
				{{--            </a> --}}
			</div>
			<div class="navbar-text" @if (isset($branding)) style="{{ $branding['textstyle'] }}" @endif>
				@stack('testname')
			</div>
		</nav>

		<div class="progress p-1" role="progressbar" aria-label="Animated striped example" aria-valuenow="0" aria-valuemin="0"
			aria-valuemax="100" id='progress-bar'>
			<div class="progress-bar progress-bar-striped progress-bar-animated"
				style="@if (isset($branding)) {{ $branding['navstyle'] }}; @endif width: 0;" id='progress-stripe'>
			</div>
		</div>

		{{--    <div class="container-fluid mt-2"> --}}
		{{-- Область отображения сообщений --}}
		<div class="row mt-2" style="margin-left: 0; margin-right: 0;">
			{{-- Область тестирования --}}
			<div class="container">
				<div class="row module-wrapper">
					@yield('content_full')
				</div>
			</div>
		</div>
	</div>

	<script src="{{ asset('assets/front/js/front.js') }}"></script>
	<script>
		// Инициализация pusher'а
		toastr.options = {
			"closeButton": true,
			"debug": false,
			"newestOnTop": true,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "0",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}

		// Broadcast
		let pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
			cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
		});

		let channel = pusher.subscribe('neuroprofile-channel-{!! session()->getId() !!}');
		channel.bind('toast-event', (data) => {
			toastr[data.type](data.message, data.title);
		});

		// Ошибки и сообщения
		@if (isset($errors) && $errors->any())
			@foreach ($errors->all() as $error)
				toastr['error']("{!! $error !!}");
			@endforeach
		@endif

		@if (session()->has('error'))
			toastr['error']("{!! session('error') !!}");
			@php
				session()->forget('error');
			@endphp
		@endif

		@if (session()->has('success'))
			toastr['success']("{!! session('success') !!}");
			@php
				session()->forget('success');
			@endphp
		@endif

		@if (session()->has('info'))
			toastr['success']("{!! session('info') !!}");
			@php
				session()->forget('info');
			@endphp
		@endif
	</script>
	<!-- Page file / URL scripts -->
	@stack('scripts')
	<!-- Page manual scripts -->
	@stack('scripts.injection')

</body>

</html>
