@extends('layouts.blocks')

@section('service')Работа с клиентами и контрактами@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Клиент', 'active' => false, 'context' => 'client', 'link' => route('clients.index', ['sid' => session()->getId()])],
			['title' => 'Контракт', 'active' => false, 'context' => 'contract', 'link' => route('contracts.index', ['sid' => session()->getId()])],
			['title' => 'Информация о контракте', 'active' => true, 'context' => 'info'],
		];
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
				<table class="statistics">
					@foreach($statistics as $key => $value)
						<tr>
							<td class="key col-md-4">{{ $key }}:</td>
							<td class="value col-md-4">{{ $value }}</td>
						</tr>
					@endforeach
				</table>
			</div>
{{--			<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">--}}
{{--				<div class="row">--}}
{{--				</div>--}}
{{--			</div>--}}
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
				<i style="color: red">Здесь будет код фреймов после реализации тестов</i>
			</div>
		</div>
	</div>
@endsection
