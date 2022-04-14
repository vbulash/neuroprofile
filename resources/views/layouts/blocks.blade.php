@extends('layouts.backend')

@yield('steps')

@section('content')
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@yield('service')</h1>
			</div>

			@php
				$context = session('context');
			@endphp
			<div class="row items-push">
				@foreach($steps as $step)
					@php
						if($loop->first) $left = true;
						if($step['active']) {
							$left = false;
							$class = 'block block-rounded block-transparent block-link-pop bg-xsmooth h-100 mb-0';
							$text = 'fs-lg fw-semibold mb-0 text-white';
							$icon = 'fas fa-2x fa-chevron-right text-white-50';
						} elseif ($left) {
							$class = 'block block-rounded block-transparent block-link-pop bg-xeco h-100 mb-0';
							$text = 'fs-lg fw-semibold mb-0 text-white';
							$icon = 'fas fa-2x fa-check text-white-50';
						} else {
							$class = 'block block-rounded block-link-shadow h-100 mb-0';
							$text = 'fs-lg fw-semibold mb-0 text-muted';
							$icon = 'fas fa-2x fa-chevron-right text-muted';
						}

						if($loop->last) {
                            $icon = 'fas fa-2x fa-stop text-white-50';
						}

                        if(isset($step['link'])) {
                            $link = $step['link'];
                        } else {
                            $class .= ' no-link';
                            $link = 'javascript:void(0)';
                        }

                        $subtitle = '';
						if(isset($step['context']))
                            if(isset($context[$step['context']]))
                            	$subtitle = $context[$step['context']]->getTitle();
					@endphp

					<div class="col-md-6 col-xl-3 mb-4">
						<a class="{!! $class !!}"
						   href="{!! $link !!}">
							<div
								class="block-content block-content-full d-flex align-items-center justify-content-between">
								<div>
									<p class="{!! $text !!}">{{ $step['title'] }}
										@if($subtitle):<br/><br/><small>{!! $subtitle !!}</small> @endif</p>
								</div>

								<div class="ms-3 item">
									<i class="{!! $icon !!}"></i>
								</div>
							</div>
						</a>
					</div>
				@endforeach
			</div>
		</div>
	</div>
	<div class="content">
		<div class="row">
			@yield('blocks')
		</div>
	</div>
@endsection


