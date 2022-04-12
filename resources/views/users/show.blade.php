@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Просмотр анкеты пользователя &laquo;{{ $user->name }}&raquo;</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Настройки</li>
						<li class="breadcrumb-item active" aria-current="page">Пользователи</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<div class="block block-rounded">
			<div class="block-content pb-3">
				<form class="mb-5" action="{{ route('users.index', ['sid' => session()->getId()]) }}"
					  method="GET">
					@csrf
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="fio">Фамилия, имя и отчество</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="fio" name="fio" value="{{ $user->name }}" disabled>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="email">Электронная почта</label>
						<div class="col-sm-5">
							<input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="role">Роль пользователя</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="role" name="role"
								   value="{!! $user->getRoleNames()->join(",<br/>") !!}" disabled>
						</div>
					</div>
					<div class="row mb-4">
						<div class="col-sm-3 col-form-label">&nbsp;</div>
						<div class="col-sm-5">
							<button type="submit" class="btn btn-primary">Закрыть</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection
