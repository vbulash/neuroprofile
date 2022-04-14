@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@yield('title')</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						@foreach($breadcrumbs as $breadcrumb)
							@if($loop->last)
								<li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb }}</li>
							@else
								<li class="breadcrumb-item">{{ $breadcrumb }}</li>
							@endif
						@endforeach
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<!-- Table -->
		<div class="block block-rounded">
			@yield('header')
			<div class="block-content pb-3">
				@yield('interior')
			</div>
		</div>
		<!-- END Table -->
	</div>
@endsection
