@extends('layouts.detail')

@section('service')Работа с клиентами и контрактами@endsection

@section('steps')
	@php
		$steps = [
            ['title' => 'История', 'active' => true, 'context' => 'history', 'link' => route('history.index')],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif истории тестирования № {{ $history->id }}
@endsection

@section('form.params')
	id="{{ form($history, $mode, 'id') }}" name="{{ form($history, $mode, 'name') }}"
	action="{{ form($history, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		if (!isset($card) || count($card) == 0)
            $fields[] = ['title' => 'В ходе тестирования не собиралась дополнительная информация о респонденте', 'type' => 'heading'];
        else {
            $fields[] = ['title' => 'В ходе тестирования собрана дополнительная информация о респонденте', 'type' => 'heading'];
            foreach ($card as $field) {
                $field['required'] = $field['name'] == 'email';
                if ($field['type'] != 'email' || $mode == config('global.show'))
                    $field['disabled'] = true;
                $fields[] = $field;
            }
        }
        $fields[] = ['title' => 'Важная информация по данной записи истории тестирования', 'type' => 'heading'];
        $fields[] = ['name' => 'timestamp', 'title' => 'Прохождение тестирования', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => (new DateTime($history->done))->format('d.m.Y G:i:s')];
        $fields[] = ['name' => 'license', 'title' => 'Ключ лицензии', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $history->license->pkey];
        $fields[] = ['name' => 'client', 'title' => 'Наименование клиента', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $history->test->contract->client->getTitle()];
        $fields[] = ['name' => 'contract', 'title' => 'Номер контракта', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $history->test->contract->number];
        $fields[] = ['name' => 'test', 'title' => 'Название теста', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $history->test->getTitle()];

        $content = json_decode($history->test->content);
        $descriptions = $content->descriptions ?? null;

        $fmptype_show = $descriptions->show ?? 0;
        if ($fmptype_show == 0) $label = 'Не назначен';
        else {
            $fmptype = \App\Models\FMPType::find($fmptype_show);
            $label = isset($fmptype) ? $fmptype->name : "Ошибка: не найдена запись типа описания ID $fmptype_show";
        }
        $fields[] = ['name' => 'results_show', 'title' => 'Тип описания: показ результата тестирования на экране', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $label];

        $fmptype_mail = $descriptions->mail ?? 0;
        if ($fmptype_mail == 0) $label = 'Не назначен';
        else {
            $fmptype = \App\Models\FMPType::find($fmptype_mail);
            $label = isset($fmptype) ? $fmptype->name : "Ошибка: не найдена запись типа описания ID $fmptype_mail";
        }
        $fields[] = ['name' => 'results_mail', 'title' => 'Тип описания: письмо респонденту с результатом тестирования', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $label];

        $fmptype_client = $descriptions->client ?? 0;
        if ($fmptype_client == 0) $label = 'Не назначен';
        else {
            $fmptype = \App\Models\FMPType::find($fmptype_client);
            $label = isset($fmptype) ? $fmptype->name : "Ошибка: не найдена запись типа описания ID $fmptype_client";
        }
        $fields[] = ['name' => 'results_client', 'title' => 'Тип описания: письмо клиенту с результатом тестирования', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $label];

        $fields[] = ['name' => 'commercial', 'title' => 'Контракт коммерческий?', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $history->test->contract->commercial ? 'Да' : 'Нет'];
        $fields[] = ['name' => 'paid', 'title' => 'Результат тестирования оплачен', 'type' => 'checkbox', 'required' => false, 'value' => $history->paid];
        $fields[] = ['name' => 'code', 'title' => 'Вычисленный код результата', 'type' => 'text', 'required' => false, 'disabled' => true, 'value' => $history->code];
	@endphp
@endsection

@section('form.close')
	{{ form($history, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let paid = document.getElementById('paid');
		paid.addEventListener('change', (event) => {
			if (event.target.checked) {	// Работает в учебном заведении
				event.target.parentElement.querySelector('label').innerText = 'Результат тестирования оплачен';
			} else {	// Работает у работодателя
				event.target.parentElement.querySelector('label').innerText = 'Результат тестирования не оплачен';
			}
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			paid.focus({preventScroll: true});
			paid.dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
