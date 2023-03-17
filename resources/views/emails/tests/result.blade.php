@if ($history->test->paid)
	@if ($history->paid == '0')
		<h3>
			Вы получили краткую бесплатную версию результата тестирования.<br />
			Оплатите полный результат тестирования и получите его по электронной почте.<br />
		</h3>
		<p>Нажимая ссылку оплаты ниже и выполняя оплату, вы соглашаетесь с условиями <a
				href="{{ route('player.policy', ['document' => 'oferta', 'mail' => false]) }}">публичного
				договора-оферты</a></p>
		<x-robokassa.link :history="$history" description="Оплата полного результата нейротестирования">
			Оплата через Робокассу
		</x-robokassa.link>
		{{--        @php --}}
		{{--            $rk = new \App\Http\Payment\Robokassa($test); --}}
		{{--            $rk->setMail(true); --}}
		{{--            $rk->setInvoice($history->id); --}}
		{{--            $rk->setEmail($card['Электронная почта']); --}}
		{{--            $rk->setDescription('Оплата полного результата нейротестирования'); --}}
		{{--            $button = $rk->getHTMLLink(); --}}
		{{--        @endphp --}}

		{{--        {!! $button !!} --}}
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
	<p>Вы прошли тестирование по тесту &laquo;{{ $history->test->name }}&raquo; @if ($history->paid == '1')
			и оплатили получение
			полных результатов тестирования
		@endif
	</p>
@endif

@if ($history->test->options & (\App\Models\TestOptions::AUTH_FULL->value | \App\Models\TestOptions::AUTH_MIX->value))
	<h1>Перед прохождением теста вы ввели анкетные данные:</h1>
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
			Полный результат вашего тестирования:
		@else
			Краткий результат вашего тестирования:
		@endif
	@else
		Результат вашего тестирования:
	@endif
</h1>
<h4>Наименование нейропрофиля: {{ $profile->name }}</h4>

@forelse($blocks  as $block)
	@if ($block->type != \App\Models\BlockType::Image->value)
		<h2>{{ $block->name }}</h2>
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
