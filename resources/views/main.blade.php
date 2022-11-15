@extends('layouts.backend')

@section('content')
	<div class="content">
		{{-- <form action="{{ route('neural.net.done') }}" method="POST" id="form-net-done">
			<input type="hidden" name="uuid" id="uuid" value="pkey_628c8b6d245934.76213557">
			<input type="hidden" name="result" id="result">
			<button type="submit" class="btn btn-primary">Проверка net.done</button>
		</form> --}}
		<!-- Статистика -->
		<div class="row">
			<div class="col">
				<div class="block block-rounded">
					<div class="block-header block-header-default">
						<h3 class="block-title">Статистика тестирования</h3>
						<div class="block-options">
							<button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"><i
									class="si si-arrow-up"></i></button>
						</div>
					</div>
					<div class="block-content row">
						<!-- Всего тестов пройдено -->
						<div class="col-md-6 col-xl-3">
							<a class="block block-rounded block-link-shadow bg-success" href="{{ route('history.index', ['sid' => $sid]) }}">
								<div class="block-content block-content-full d-flex align-items-center justify-content-between">
									<div>
										<i class="fa fa-2x fa-chart-line text-white"></i>
									</div>
									<div class="ms-3 text-end">
										<p class="text-white fs-3 fw-medium mb-0">
											{{ $data[\App\Http\Controllers\ReportDataController::HISTORY_ALL_COUNT] }}</p>
										<p class="text-white mb-0">
											{{ $data[\App\Http\Controllers\ReportDataController::HISTORY_ALL_COUNT . '.letter'] }}</p>
									</div>
								</div>
							</a>
						</div>
						<!-- /.Всего тестов пройдено -->
						<!-- Всего тестов оплачено -->
						<div class="col-md-6 col-xl-3">
							<a class="block block-rounded block-link-shadow bg-warning" href="{{ route('history.index', ['sid' => $sid]) }}">
								<div class="block-content block-content-full d-flex align-items-center justify-content-between">
									<div>
										<i class="fa fa-2x fa-coins text-white"></i>
									</div>
									<div class="ms-3 text-end">
										<p class="text-white fs-3 fw-medium mb-0">
											{{ $data[\App\Http\Controllers\ReportDataController::HISTORY_PAID_COUNT] }}</p>
										<p class="text-white mb-0">
											{{ $data[\App\Http\Controllers\ReportDataController::HISTORY_PAID_COUNT . '.letter'] }}</p>
									</div>
								</div>
							</a>
						</div>
						<!-- /.Всего тестов оплачено -->
					</div>
				</div>
			</div>
		</div>
		<!-- /.Статистика -->

		<!-- Динамика -->
		<div class="row">
			<div class="col">
				<div class="block block-rounded">
					<div class="block-header block-header-default">
						<h3 class="block-title">Динамика тестирования</h3>
						<div class="block-options">
							<button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"><i
									class="si si-arrow-up"></i></button>
							</button>
						</div>
					</div>
					<div class="block-content row">
						<div class="chart">
							<canvas id="stackedBarChart" style="min-height:330px"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.Динамика -->
	</div>
@endsection

@push('js_before')
	<script src="{{ asset('js/chart.min.js') }}"></script>
@endpush

@push('js_after')
	<script>
		let areaChartData = {
			labels: null,
			datasets: [{
					label: 'Тестирований пройдено',
					backgroundColor: '#007bff',
					data: null
				},
				{
					label: 'Тестирований оплачено',
					backgroundColor: '#28a745',
					data: null
				},
			]
		};
		areaChartData.labels = [{!! "'" . implode("', '", $data[\App\Http\Controllers\ReportDataController::HISTORY_DYNAMIC_LABELS]) . "'" !!}];
		areaChartData.datasets[0].data = [
			{{ implode(', ', $data[\App\Http\Controllers\ReportDataController::HISTORY_DYNAMIC_ALL_COUNT]) }}
		];
		areaChartData.datasets[1].data = [
			{{ implode(', ', $data[\App\Http\Controllers\ReportDataController::HISTORY_DYNAMIC_PAID_COUNT]) }}
		];

		let areaChartOptions = {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				title: {
					display: false,
					text: 'Динамика тестирования'
				},
				legend: {
					position: 'bottom'
				}
			},
			scales: {
				xAxes: {
					ticks: {
						autoSkip: false,
						maxRotation: 90,
						minRotation: 0
					}
				}
			}
		};

		let ctx = document.getElementById('stackedBarChart').getContext('2d');
		let myChart = new Chart(ctx, {
			type: 'bar',
			data: areaChartData,
			options: areaChartOptions
		});

// 		document.getElementById('form-net-done').addEventListener('submit', () => {
// 			document.getElementById('result').value = `
// [
// 	{
// 		"code": "A",
// 		"average": 0.7,
// 		"meansquare": 0.01
// 	},
// 	{
// 		"code": "B",
// 		"average": 0.1,
// 		"meansquare": 0.2
// 	},
// 	{
// 		"code": "C",
// 		"average": 0.1,
// 		"meansquare": 0.3
// 	}
// ]
// 			`;
// 		}, false);
	</script>
@endpush
