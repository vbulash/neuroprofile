@extends('tests.steps.wizard')

@section('service')Создание теста@endsection

@section('interior.subheader') @endsection

@section('form.fields')
	@php
        $heap = session('heap');
        $options = intval($heap['options']);
        if (isset($heap) && isset($heap['step-payment'])) {
            $fields = [
				['name' => 'merchant', 'title' => 'Магазин Robokassa', 'required' => true, 'type' => 'text', 'value' => $heap['robokassa']['merchant'] ?? ''],
				['name' => 'password', 'title' => 'Пароль магазина Robokassa', 'required' => true, 'type' => 'password', 'value' => $heap['robokassa']['password'] ?? ''],
				['name' => 'sum', 'title' => 'Сумма оплаты за платный результат тестирования Robokassa', 'required' => true, 'type' => 'number', 'value' => $heap['robokassa']['sum'] ?? '', 'min' => 1],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid]
			];
        } else {
			$fields = [
				['name' => 'merchant', 'title' => 'Магазин Robokassa', 'required' => true, 'type' => 'text'],
				['name' => 'password', 'title' => 'Пароль магазина Robokassa', 'required' => true, 'type' => 'password'],
				['name' => 'sum', 'title' => 'Сумма оплаты за платный результат тестирования Robokassa', 'required' => true, 'type' => 'number', 'min' => 1, 'value' => 500],
				['name' => 'mode', 'type' => 'hidden', 'value' => $mode],
				['name' => 'test', 'type' => 'hidden', 'value' => $test],
				['name' => 'sid', 'type' => 'hidden', 'value' => $sid]
			];
        }
	@endphp
@endsection

@section('form.before.content')
	<div class="col-sm-8 mb-4 p-4">
		<div class="checkbox">
			<label>
				<input type="checkbox" id="payment-option" name="payment-option"
					   @if ($options & \App\Models\TestOptions::CUSTOM_PAYMENT->value)
						   checked
					   @endif
				> Тест имеет самостоятельную оплату, отличную от встроенной</label>
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
