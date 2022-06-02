@extends('tests.steps.wizard')

@section('service')
	@if ($mode == config('global.create'))
		Создание теста
	@else
		@php
			$heap = session('heap');
		@endphp
		@if ($mode == config('global.show'))
			Просмотр
		@else
			Редактирование
		@endif теста &laquo;{{ $heap['name'] }}&raquo;
	@endif
@endsection

@section('interior.subheader') @endsection

@section('form.fields')
	@php
		$heap = session('heap');
		$options = intval($heap['options'] ?? 0);

        if (!isset($heap['step-payment']) && $mode == config('global.create')) {
            $custom = false;
            $fields = [
				['name' => 'merchant', 'title' => 'Магазин Robokassa', 'required' => true, 'type' => 'text'],
				['name' => 'password', 'title' => 'Пароль магазина Robokassa', 'required' => true, 'type' => 'password'],
				['name' => 'sum', 'title' => 'Сумма оплаты за платный результат тестирования Robokassa', 'required' => true, 'type' => 'number', 'min' => 1, 'value' => 500],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid],
				['name' => 'step-payment', 'type' => 'hidden', 'value' => true]
			];
        } else {
            $custom = $options & \App\Models\TestOptions::CUSTOM_PAYMENT->value;
            $fields = [
				['name' => 'merchant', 'title' => 'Магазин Robokassa', 'required' => true, 'type' => 'text', 'value' => $heap['robokassa']['merchant'] ?? ''],
				['name' => 'password', 'title' => 'Пароль магазина Robokassa', 'required' => true, 'type' => 'password', 'value' => $heap['robokassa']['password'] ?? ''],
				['name' => 'sum', 'title' => 'Сумма оплаты за платный результат тестирования Robokassa', 'required' => true, 'type' => 'number', 'value' => $heap['robokassa']['sum'] ?? '', 'min' => 1],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid],
				['name' => 'step-payment', 'type' => 'hidden', 'value' => true]
			];
        }
	@endphp
@endsection

@section('form.before.content')
	<div class="col-sm-8 mb-4 p-4">
		<div class="form-check form-switch">
			<input class="form-check-input"
				   type="checkbox"
				   id="payment-option" name="payment-option"
				   @if($custom)
					   checked
				   @endif
				   @if($mode == config('global.show')) disabled @endif>
			<label class="form-check-label" for="payment-option">Тест имеет самостоятельную оплату, отличную от встроенной</label>
		</div>
	</div>
@endsection

@push('js_after')
	<script>
		document.getElementById('payment-option').addEventListener('change', (event) => {
			if (event.target.checked) {
				document.getElementById('content').style.display = 'block';
			} else {
				document.getElementById('content').style.display = 'none';
			}
		});

		document.addEventListener("DOMContentLoaded", () => {
			if (document.getElementById('payment-option').checked) {
				document.getElementById('content').style.display = 'block';
			} else {
				document.getElementById('content').style.display = 'none';
			}
		}, false);
	</script>
@endpush
