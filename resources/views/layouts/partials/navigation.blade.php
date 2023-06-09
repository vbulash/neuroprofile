<div class="content-side content-side-full">
	@php
		$admin = false;
		if (
		    auth()
		        ->user()
		        ->hasRole(App\Http\Controllers\Auth\RoleName::ADMIN->value)
		) {
		    $admin = true; // TODO Разрешения в зависимости от роли пользователя
		}

		$menu = [];
		$menu[] = ['title' => 'Главная', 'icon' => 'fa fa-home', 'route' => 'dashboard', 'pattern' => 'dashboard'];

		$menu[] = ['title' => 'Клиенты', 'heading' => true];
		$menu[] = ['title' => 'Клиенты и контракты', 'icon' => 'fas fa-building', 'route' => 'clients.index', 'pattern' => ['clients.*', 'contracts.*']];

		$menu[] = ['title' => 'Конструктор тестов', 'heading' => true];
		$menu[] = ['title' => 'Вопросы тестов', 'icon' => 'fas fa-question-circle', 'route' => 'sets.index', 'pattern' => ['sets.*', 'questions.*', 'parts.*']];

		if (env('EXEC_MODE') != 'research') {
		    $menu[] = ['title' => 'Обработка результатов', 'icon' => 'fas fa-drafting-compass', 'route' => 'fmptypes.index', 'pattern' => ['fmptypes.*', 'profiles.*', 'blocks.*', 'aliases.*', 'clones.*', 'texts.*', 'images.*']];
		    $menu[] = ['title' => 'Ссылочные блоки', 'icon' => 'fas fa-link', 'route' => 'parents.index', 'pattern' => ['parents.*']];
		}
		$menu[] = ['title' => 'Тесты', 'icon' => 'fas fa-drafting-compass', 'route' => 'tests.index', 'pattern' => ['tests.*']];

		$menu[] = ['title' => 'Прохождение тестов', 'heading' => true];
		$menu[] = ['title' => 'Проверочный плеер', 'icon' => 'fas fa-play-circle', 'modal' => 'tests-play', 'pattern' => []];
		$menu[] = ['title' => 'История прохождения', 'icon' => 'fas fa-file-video', 'route' => 'history.index', 'pattern' => ['history.*']];

		$menu[] = ['title' => 'Настройки', 'heading' => true];
		$menu[] = ['title' => 'Администраторы платформы', 'icon' => 'fa fa-user-alt', 'route' => 'admins.index', 'pattern' => 'admins.*'];
		$menu[] = ['title' => 'Аккаунт менеджеры', 'icon' => 'fa fa-user-alt', 'route' => 'adminclients.index', 'pattern' => 'adminclients.*'];
		$menu[] = ['title' => 'Типы вопросов', 'icon' => 'fa fa-question-circle', 'route' => 'kinds.index', 'pattern' => 'kinds.*'];
		if ($admin) {
		    $menu[] = ['title' => 'Laravel Telescope', 'icon' => 'fas fa-gear', 'link' => '/telescope', 'target' => '_blank', 'pattern' => []];
		}
	@endphp
	<ul class="nav-main">
		@foreach ($menu as $item)
			@if (isset($item['heading']))
				<li class="nav-main-heading">{{ $item['title'] }}</li>
			@else
				<li class="nav-main-item">
					<a class="nav-main-link{{ request()->routeIs($item['pattern']) ? ' active' : '' }}"
						@if (isset($item['modal'])) href="javascript:void(0)"
						   data-bs-toggle="modal" data-bs-target="#{{ $item['modal'] }}"
					   	@elseif (isset($item['route']))
						   href="{{ route($item['route']) }}"
						@elseif (isset($item['link']))
						   href="{{ $item['link'] }}"
						   @if (isset($item['target']))
								target="{{ $item['target'] }}" @endif
						@endif>
						<i class="nav-main-link-icon {{ $item['icon'] }}"></i>
						<span class="nav-main-link-name">{{ $item['title'] }}</span>
					</a>
				</li>
			@endif
		@endforeach
	</ul>
</div>
