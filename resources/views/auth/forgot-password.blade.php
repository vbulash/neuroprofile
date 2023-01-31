@extends('layouts.skeleton')

@section('body')
	<div id="page-container">

		<!-- Main Container -->
		<main id="main-container">
			<!-- Page Content -->
			<div class="bg-image" style="background-image: url('{{ asset('media/photos/moscow_city_12.jpeg') }}');">
				<div class="row g-0 justify-content-center bg-primary-dark-op">
					<div class="hero-static col-sm-8 col-md-6 col-xl-4 d-flex align-items-center p-2 px-sm-0">
						<div class="block block-transparent block-rounded w-100 mb-0 overflow-hidden">
							<div
								class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-body-extra-light">
								<!-- Header -->
								<div class="mb-2 text-center">
									<a class="link-fx fw-bold fs-3" href="javascript:void(0)">
										<span class="text-dark">{!! env('APP_NAME') !!}</span>
									</a>
									<p class="text-uppercase fw-bold fs-sm text-muted">Сброс пароля</p>
								</div>
								<!-- END Header -->

								<form method="POST" action="{{ route('password.email') }}">
									@csrf
									<div class="mb-4 text-sm text-gray-600">
										Забыли пароль? Нет проблем. Введите адрес электронной почты пользователя платформы &laquo;{{ env('APP_NAME') }}&raquo;, на которую мы пришлем ссылку сброса пароля, что позволит ввести вам новый пароль
									</div>
									<div class="form-floating mb-4">
										<input type="email" class="form-control" id="email" name="email"
											   placeholder="Электронная почта">
										<label for="email">Электронная почта пользователя</label>
									</div>
									<div class="text-center mt-4 mb-4">
										<button type="submit" class="btn btn-hero btn-primary">
											<i class="fa fa-fw fa-eraser opacity-50 me-1"></i> Сбросить пароль
										</button>
									</div>
								</form>
							</div>
						</div>
						<!-- END Sign In Block -->
					</div>
				</div>
			</div>
			<!-- END Page Content -->
		</main>
		<!-- END Main Container -->
	</div>
	<!-- END Page Container -->
@endsection
