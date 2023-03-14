@extends('layouts.skeleton')

@section('body')
	<div id="page-container">

		<!-- Main Container -->
		<main id="main-container">
			<!-- Page Content -->
			<div class="bg-image" style="background-image: url('{{ asset('media/photos/moscow_city_12.jpeg') }}');">
				<div class="row g-0 justify-content-center bg-primary-dark-op">
					<div class="hero-static col-sm-8 col-md-6 col-xl-4 d-flex align-items-center p-2 px-sm-0">
						<!-- Sign In Block -->
						<div class="block block-transparent block-rounded w-100 mb-0 overflow-hidden">
							<div class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-body-extra-light">
								<!-- Header -->
								<div class="mb-2 text-center">
									<a class="link-fx fw-bold fs-3" href="javascript:void(0)">
										<span class="text-dark">{!! env('APP_NAME') !!}</span>
									</a>
									<p class="text-uppercase fw-bold fs-sm text-muted">Регистрация новой учетной
										записи</p>
								</div>
								<!-- END Header -->

								<form method="POST" action="{{ route('register') }}">
									@csrf
									<div class="mb-4">
										<div class="input-group input-group-lg">
											<input type="text" class="signup form-control" id="signup-adminname" name="name"
												placeholder="Фамилия, имя и отчество">
											<span class="input-group-text">
												<i class="fa fa-admin-circle"></i>
											</span>
										</div>
									</div>
									<div class="mb-4">
										<div class="input-group input-group-lg">
											<input type="email" class="signup form-control" id="signup-email" name="email"
												placeholder="Электронная почта">
											<span class="input-group-text">
												<i class="fa fa-envelope-open"></i>
											</span>
										</div>
									</div>
									<div class="mb-4">
										<div class="input-group input-group-lg">
											<input type="password" class="signup form-control" id="signup-password" name="password" placeholder="Пароль">
											<span class="input-group-text">
												<i class="fa fa-asterisk"></i>
											</span>
										</div>
									</div>
									<div class="mb-4">
										<div class="input-group input-group-lg">
											<input type="password" class="signup form-control" id="password_confirmation" name="password_confirmation"
												placeholder="Подтверждение пароля">
											<span class="input-group-text">
												<i class="fa fa-asterisk"></i>
											</span>
										</div>
									</div>
									<div class="mb-4">
										<div class="input-group input-group-lg">
											<select name="role" id="role" class="form-control">
												<option selected disabled>Выберите роль нового пользователя из списка
												</option>
												@foreach ($roles as $role)
													<option value="{{ $role }}">{!! $role !!}</option>
												@endforeach
											</select>
											<span class="input-group-text">
												<i class="fa fa-chevron-down"></i>
											</span>
										</div>
									</div>

									<div class="d-sm-flex justify-content-sm-between align-items-sm-center mb-4 bg-body rounded py-2 px-3">
										<div class="form-check">
											<input type="checkbox" class="signup form-check-input" id="terms" name="terms">
											<label class="form-check-label" for="terms">Я соглашаюсь с Политикой
												конфиденциальности</label>
										</div>
										<div class="fw-semibold fs-sm py-1">
											<a class="fw-semibold fs-sm" href="#" data-bs-toggle="modal" data-bs-target="#modal-terms">Политика
												конфиденциальности</a>
										</div>
									</div>
									<div class="text-center mb-4">
										<button type="submit" class="btn btn-hero btn-primary" id="submit_btn">
											<i class="fa fa-fw fa-plus opacity-50 me-1"></i> Зарегистрировать
										</button>
									</div>
								</form>
							</div>
						</div>
						<!-- END Sign In Block -->
					</div>
				</div>

				<!-- Terms Modal -->
				<div class="modal fade" id="modal-terms" tabindex="-1" role="dialog" aria-labelledby="modal-terms"
					aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="block block-themed block-transparent mb-0">
								<div class="block-header bg-success">
									<h3 class="block-title">Политика конфиденциальности</h3>
									<div class="block-options">
										<button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
											<i class="fa fa-fw fa-times"></i>
										</button>
									</div>
								</div>
								<div class="block-content">
									<p>Potenti elit lectus augue eget iaculis vitae etiam, ullamcorper etiam bibendum ad
										feugiat magna accumsan dolor, nibh molestie cras hac ac ad massa, fusce ante
										convallis ante urna molestie vulputate bibendum tempus ante justo arcu erat
										accumsan adipiscing risus, libero condimentum venenatis sit nisl nisi ultricies
										sed, fames aliquet consectetur consequat nostra molestie neque nullam
										scelerisque neque commodo turpis quisque etiam egestas vulputate massa,
										curabitur tellus massa venenatis congue dolor enim integer luctus, nisi suscipit
										gravida fames quis vulputate nisi viverra luctus id leo dictum lorem, inceptos
										nibh orci.</p>
									<p>Potenti elit lectus augue eget iaculis vitae etiam, ullamcorper etiam bibendum ad
										feugiat magna accumsan dolor, nibh molestie cras hac ac ad massa, fusce ante
										convallis ante urna molestie vulputate bibendum tempus ante justo arcu erat
										accumsan adipiscing risus, libero condimentum venenatis sit nisl nisi ultricies
										sed, fames aliquet consectetur consequat nostra molestie neque nullam
										scelerisque neque commodo turpis quisque etiam egestas vulputate massa,
										curabitur tellus massa venenatis congue dolor enim integer luctus, nisi suscipit
										gravida fames quis vulputate nisi viverra luctus id leo dictum lorem, inceptos
										nibh orci.</p>
								</div>
								<div class="block-content block-content-full text-end bg-body">
									<button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Готово
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- END Terms Modal -->
			</div>
			<!-- END Page Content -->
		</main>
		<!-- END Main Container -->
	</div>
	<!-- END Page Container -->
@endsection

@push('js_after')
	<script>
		document.addEventListener("DOMContentLoaded", () => {}, false);
	</script>
@endpush
