@extends('layouts.backend')

@section('content')
	@php
		$cards = [
    		[
    			['role' => 'Работодатель', 'title' => 'Работодатель', 'subtitle' => 'Начать стажировку практиканта', 'class' => 'bg-gd-sea', 'icon' => 'fa fa-2x fa-business-time', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
    			['role' => 'Работодатель', 'title' => 'Работодатель', 'subtitle' => 'Отслеживать стажировку', 'class' => 'bg-gd-sea', 'icon' => 'fa fa-2x fa-business-time', 'link' => 'javascript:void(0)'],
    			['role' => 'Работодатель', 'title' => 'Работодатель', 'subtitle' => 'Завершить стажировку', 'class' => 'bg-gd-sea', 'icon' => 'fa fa-2x fa-business-time', 'link' => 'javascript:void(0)'],
    		],
    		[
    			['role' => 'Практикант', 'title' => 'Практикант', 'subtitle' => 'Пройти стажировку', 'class' => 'bg-gd-dusk', 'icon' => 'fas fa-2x fa-user-graduate', 'link' => 'javascript:void(0)'],
    		]
		];
	@endphp
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Платформа позволяет оказывать следующие услуги</h1>
			</div>
{{--			<div class="row items-push">Дополнительный блок</div>--}}
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-12">
				<div class="block block-rounded">
{{--					<div class="block-header block-header-default">--}}
{{--						<h3 class="block-title">Title <small>Subtitle</small></h3>--}}
{{--					</div>--}}
					<div class="block-content">
						@foreach($cards as $row)
							<div class="row items-push">
								@foreach($row as $card)
									@php
										$allowed = false;
										if (auth()->user()->hasRole('Администратор')) {
											$allowed = true;
										} elseif (auth()->user()->hasRole('Работодатель') && $card['role'] == 'Работодатель') {
											$allowed = true;
										} elseif (auth()->user()->hasRole('Практикант') && $card['role'] == 'Практикант') {
											$allowed = true;
										}
									@endphp

									@if(!$allowed)
										@continue
									@endif

									<div class="col-md-6 col-xl-4 mb-4">
										<a class="block block-rounded block-transparent block-link-pop {{ $card['class'] }} h-100 mb-0"
										   href="{{ $card['link'] }}">
											<div
												class="block-content block-content-full d-flex align-items-center justify-content-between">
												<div>
													<p class="fs-lg fw-semibold mb-0 text-white">{{ $card['title'] }}</p>
													<p class="text-white-75 mb-0">{{ $card['subtitle'] }}</p>
												</div>
												<div class="ms-3 item">
													<i class="{{ $card['icon'] }} text-white-50"></i>
												</div>
											</div>
										</a>
									</div>
								@endforeach
							</div>
						@endforeach
					</div>
				</div>
			</div>


		</div>
	</div>
@endsection
