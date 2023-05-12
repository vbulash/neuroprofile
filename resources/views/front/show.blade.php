@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $history->test->name }}&raquo;
@endpush

@push('testname')
	{{ $history->test->name }}
@endpush

@section('content_full')
	<div class="col-md-12 p-2">
		@auth
			<a href="{{ route('dashboard') }}" class="btn btn-primary mt-1">Возврат на главную страницу</a>
		@endauth

		@php
			if (session('branding')) {
			    $style = session('branding')['buttonstyle'];
			    $class = '';
			} else {
			    $style = '';
			    $class = 'btn-primary';
			}
		@endphp
		@if ($history->test->paid)
			@if ($history->paid == '0')
				<h5 class="mt-4">Вы видите краткую бесплатную версию результатов тестирования.<br />
					Оплатите полный результат тестирования и получите его по электронной почте.<br />
				</h5>
				<p>Нажимая кнопку оплаты ниже и выполняя оплату, вы соглашаетесь с условиями <a
						href="{{ route('player.policy', ['document' => 'oferta']) }}" target="_blank">публичного
						договора-оферты</a></p>
				<x-robokassa.frame :history="$history" description="Оплата полного результата нейротестирования"
					class="{{ $class }}" style="{{ $style }}">
					Оплата через Робокассу
				</x-robokassa.frame>
			@endif
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
				@if (!($history->test->options & \App\Models\TestOptions::DONT_SHOW_TITLE->value))
					@if ($block->show_title)
						<h2>{{ $block->name }}</h2>
					@endif
				@else
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
						<img src="{{ $image }}" class="img-fluid" />
					</div>
				@break

				@default
			@endswitch
			@empty
				<h2>Настройка теста не завершена.<br />
					Нет блоков описаний, соответствующих коду нейропрофиля &laquo;{{ $profile->code }}&raquo;</h2>
			@endforelse
		</div>
	@endsection

	@push('scripts.injection')
		<script>
			document.addEventListener("DOMContentLoaded", () => {
				//
			});
		</script>
	@endpush
