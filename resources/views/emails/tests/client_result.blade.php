@if ($history->test->paid)
	@if ($history->paid == '0')
		<h3>
			Это краткая бесплатная версия результата тестирования.<br />
			Респондент может оплатить получение полного результата тестирования по ссылке из индивидуального письма.<br />
		</h3>
	@endif
@endif

@if (isset($card['Пол']) || isset($card['Имя']) || isset($card['Фамилия']))
	@php
		if (!isset($card['Пол'])) {
		    $card['Пол'] = '';
		}
		if (!isset($card['Имя'])) {
		    $card['Имя'] = '';
		}
		if (!isset($card['Фамилия'])) {
		    $card['Фамилия'] = '';
		}
		
		$greeting = match ($card['Пол']) {
		    'М' => 'Уважаемый',
		    'Ж' => 'Уважаемая',
		    default => '',
		};
	@endphp
	<h1>{{ $greeting }} {{ $card['Имя'] . ' ' . $card['Фамилия'] }}</h1>
	<p>Респондент прошел тестирование по тесту &laquo;{{ $history->test->name }}&raquo; @if ($history->paid == '1')
			и оплатил получение
			полных результатов тестирования
		@endif
	</p>
@endif

@if ($history->test->options & (\App\Models\TestOptions::AUTH_FULL->value | \App\Models\TestOptions::AUTH_MIX->value))
	<h1>Перед прохождением респондент ввел анкетные данные:</h1>
	<ul>
		@foreach ($card as $key => $value)
			@if (!$value)
				@continue
			@endif
			<li>{{ $key }} : {{ $value }}</li>
		@endforeach
	</ul>
@endif

<h1>
	@if ($history->test->paid)
		@if ($history->paid == '1')
			Полный результат тестирования респондента:
		@else
			Краткий результат тестирования респондента:
		@endif
	@else
		Результат тестирования респондента:
	@endif
</h1>
<h4>Наименование нейропрофиля: {{ $profile->name }}</h4>
<h4>Персональный ключ респондента: {{ $history->license->pkey }}</h4>

@forelse($blocks  as $block)
	@if ($block->type != \App\Models\BlockType::Image->value)
		@if ($block->show_title)
			<p><strong>{{ $block->name }}</strong></p>
		@endif
	@else
		@php
			$image = url('/uploads/' . $block->full);
		@endphp
	@endif

	@switch($block->type)
		@case(\App\Models\BlockType::Text->value)
			@if ($history->test->paid)
				@if ($history->paid == '1')
					<div style="margin-left: 20px;">{!! $block->full !!}</div>
				@else
					<div style="margin-left: 20px;">
						@if ($block->short)
							{{ $block->short }}
						@else
							{{--                    Содержание краткого / бесплатного блока... --}}
							Информация доступна в полной версии
						@endif

					</div>
				@endif
			@else
				<div style="margin-left: 20px;">{!! $block->full !!}</div>
			@endif
		@break

		@case(\App\Models\BlockType::Image->value)
			<div style="margin-left: 20px;">
				<img src="{{ $image }}" class="img-fluid"
					alt="Разрешите загрузку картинок, чтобы увидеть приложенную или приложенные к данному письму" />
			</div>
		@break

		@default
	@endswitch
	@empty
		<h2>Настройка теста не завершена.<br />
			Нет блоков описаний, соответствующих коду нейропрофиля &laquo;{{ $profile->code }}&raquo;</h2>
	@endforelse

	<div style="margin-top: 40px;">
		@if (isset($branding) && isset($branding->signature))
			{!! $branding->signature !!}
		@else
			С уважением,<br />
			<a href="{{ env('BRAND_URL') }}" target="_blank">{{ env('BRAND_NAME') }}</a>
		@endif
	</div>
