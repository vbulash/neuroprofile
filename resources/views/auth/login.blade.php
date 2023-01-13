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
                            <div
                                class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-body-extra-light">
                                <!-- Header -->
                                <div class="mb-2 text-center">
                                    <a class="link-fx fw-bold fs-1" href="javascript:void(0)">
                                        <span class="text-dark">{!! env('APP_NAME') !!}</span>
                                    </a>
                                    <p class="text-uppercase fw-bold fs-sm text-muted">Вход</p>
                                </div>
                                <!-- END Header -->

                                <form method="POST" action="{{ route('login') }}">
									@csrf
									<div class="form-floating mb-4">
										<input type="email" class="form-control" id="email" name="email"
											   placeholder="Электронная почта">
										<label for="email">Электронная почта</label>
									</div>
									<div class="form-floating mb-4">
										<input type="password" class="form-control" id="password" name="password"
											   placeholder="Пароль">
										<label for="password">Пароль</label>
									</div>

                                    <div
                                        class="d-sm-flex justify-content-sm-between align-items-sm-center text-center text-sm-start mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="remember_me"
                                                   name="login-remember-me" checked>
                                            <label class="form-check-label" for="remember_me">Запомнить меня</label>
                                        </div>
                                        <div class="fw-semibold fs-sm py-1">
                                            <a href="{{ route('password.request') }}">Забыли пароль?</a>
                                        </div>
                                    </div>
{{--                                    <div--}}
{{--                                        class="d-sm-flex justify-content-sm-between align-items-sm-center text-center text-sm-start mb-4">--}}
{{--                                        <div>&nbsp;</div>--}}
{{--                                        <div class="fw-semibold fs-sm py-1">--}}
{{--                                            <a href="{{ route('register') }}">Новый пользователь?</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="text-center mt-4 mb-4">
                                        <button type="submit" class="btn btn-hero btn-primary">
                                            <i class="fa fa-fw fa-sign-in-alt opacity-50 me-1"></i> Войти
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
