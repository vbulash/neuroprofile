<!doctype html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ env('APP_NAME') }} - Калибровка айтрекинга</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	{{--    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> --}}
	<link rel="stylesheet" href="{{ asset('assets/front/css/front.css') }}">

	<link rel="stylesheet" type="text/css" href="{{ asset('css/webgazer/webgazer.css') }}">
</head>

<body>
	<canvas id="plotting_canvas" width="500" height="500" style="cursor:crosshair;"></canvas>

	<!-- Calibration points -->
	<div class="calibrationDiv">
		<button class="Calibration" id="Pt1"></button>
		<button class="Calibration" id="Pt2"></button>
		<button class="Calibration" id="Pt3"></button>
		<button class="Calibration" id="Pt4"></button>
		<button class="Calibration" id="Pt5"></button>
		<button class="Calibration" id="Pt6"></button>
		<button class="Calibration" id="Pt7"></button>
		<button class="Calibration" id="Pt8"></button>
		<button class="Calibration" id="Pt9"></button>
	</div>

	<script>
		window.forward = "{{ route('player.body2', ['sid' => session()->getId()]) }}"
	</script>
	<script src="{{ asset('assets/front/js/front.js') }}"></script>
	<script src="{{ asset('js/webgazer/webgazer.js') }}"></script>
	<script src="{{ asset('js/webgazer/sweetalert.min.js') }}"></script>
	<script src="{{ asset('js/webgazer/main.js') }}"></script>
	<script src="{{ asset('js/webgazer/calibration.js') }}"></script>
	<script src="{{ asset('js/webgazer/precision_calculation.js') }}"></script>
	<script src="{{ asset('js/webgazer/precision_store_points.js') }}"></script>
	<script src="{{ asset('js/webgazer/resize_canvas.js') }}"></script>
</body>

</html>
