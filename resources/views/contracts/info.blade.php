@extends('layouts.blocks')

@section('service')
	Работа с клиентами и контрактами
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Клиент', 'active' => false, 'context' => 'client', 'link' => route('clients.index')], ['title' => 'Контракт', 'active' => false, 'context' => 'contract', 'link' => route('contracts.index')], ['title' => 'Информация о контракте', 'active' => true, 'context' => 'info']];
	@endphp
@endsection

@section('blocks')
	<div class="col-md-6">
		<div class="block block-rounded">
			<div class="block-header block-header-default">
				<h3 class="block-title fw-semibold">
					Статистика по лицензиям контракта
				</h3>
			</div>
			<div class="block-content p-4">
				<table class="statistics mb-4">
					@foreach ($statistics as $key => $value)
						<tr>
							<td class="key col-md-4">{{ $key }}:</td>
							<td class="value col-md-4">{{ $value }}</td>
						</tr>
					@endforeach
				</table>
				<a href="{{ route('contracts.licenses.export', ['contract' => $contract->getKey()]) }}" type="button"
					class="btn btn-primary">Экспорт лицензий</a>
			</div>
			{{--			<div class="block-content block-content-full block-content-sm bg-body-light fs-sm"> --}}
			{{--				<div class="row"> --}}
			{{--				</div> --}}
			{{--			</div> --}}
		</div>
	</div>

	<div class="col-md-6">
		<div class="block block-rounded">
			<div class="block-header block-header-default">
				<h3 class="block-title fw-semibold">
					Код HTML фреймов тестов контракта для встраивания на сайт клиента
				</h3>
			</div>
			<div class="block-content p-4">
				@php
					$testNo = 1;
				@endphp

				@forelse($contract->tests as $test)
					@php
						$code = sprintf(
						    "<iframe\n" . "src=\"%s\"\n" . "width=\"1000px\"\n" . "height=\"700px\"\n" . "allow=\"camera\"\n" . "frameborder=\"0\">\n" . '</iframe>',
						    route('player.play', [
						        'mkey' => $contract->mkey,
						        'test' => $test->key,
						    ]),
						);
					@endphp
					<div class="form-group">
						<label for="html-{{ $testNo }}">Тест # {{ $test->id }} &laquo;{{ $test->name }}&raquo;</label>
						<div class="d-flex flex-row align-items-start mt-4 mb-4">
							<textarea name="html-{{ $testNo }}" class="form-control me-2" id="html-{{ $testNo }}" cols="40"
							 rows="7" readonly>{{ $code }}</textarea>
							<a href="javascript:void(0)" class="btn btn-primary btn-sm float-left htmlcopy"
								data-code="html-{{ $testNo }}" data-test="{{ $test->name }}" data-toggle="tooltip" data-placement="top"
								title="Копировать код HTML">
								<i class="fas fa-copy" data-code="html-{{ $testNo }}" data-test="{{ $test->name }}"></i>
							</a>
						</div>
					</div>
					@php
						$testNo++;
					@endphp
				@empty
					<p>В контракте нет тестов, нет кода HTML для страниц</p>
				@endforelse
			</div>
		</div>
	</div>
@endsection

@push('js_after')
	<script>
		document.querySelectorAll('.htmlcopy').forEach(button => {
			button.addEventListener('click', event => {
				let source = event.target.dataset.code;
				let test = event.target.dataset.test;

				let copyText = document.getElementById(source);
				copyText.select();
				copyText.setSelectionRange(0, 99999);
				navigator.clipboard.writeText(copyText.value);
				copyText.setSelectionRange(0, 0);

				showToast('info', 'Код для HTML-фрейма с тестом "' + test + '" скопирован в буфер обмена',
					false);
			}, false);
		});
	</script>
@endpush
