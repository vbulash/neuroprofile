@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@if ($profile) Мой профиль @else Анкета
					пользователя &laquo;{{ $user->name }}&raquo;@endif</h1>
				@if (!$profile)
					<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item">Настройки</li>
							<li class="breadcrumb-item active" aria-current="page">Пользователи</li>
						</ol>
					</nav>
				@endif
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<div class="block block-rounded">
			<div class="block-content pb-3">
				<form role="form" class="mb-5" method="post"
					  action="{{ route('users.update', ['user' => $user->id, 'sid' => session()->getId()]) }}"
					  autocomplete="off" enctype="multipart/form-data">
					@method('PUT')
					@csrf
					<input type="hidden" name="profile" value="{{ $profile }}">
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="fio">Фамилия, имя и отчество</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="fio" name="fio" value="{{ $user->name }}">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="email">Электронная почта</label>
						<div class="col-sm-5">
							<input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
								   autocomplete="off">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="password">Новый пароль</label>
						<div class="col-sm-5">
							<input type="password" class="form-control" id="password" name="password"
								   autocomplete="new-password">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="password_confirmation">Подтверждение нового
							пароля</label>
						<div class="col-sm-5">
							<input type="password" class="form-control" id="password_confirmation"
								   name="password_confirmation">
						</div>
					</div>
					<div class="row mb-4">
						<div class="col-sm-3 col-form-label">&nbsp;</div>
						<div class="col-sm-5">
							<button type="submit" class="btn btn-primary">Сохранить</button>
							<a class="btn btn-secondary pl-3"
							   href="{{ route($profile ? 'dashboard' : 'users.index', ['sid' => session()->getId()]) }}"
							   role="button">Закрыть</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection
