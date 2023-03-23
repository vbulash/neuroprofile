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

@section('form.fields')
	@php
		$heap = session('heap');
		$options = intval($heap['options'] ?? 0);
		
		$fields = [
		    [
		        'name' => 'mode',
		        'type' => 'hidden',
		        'value' => $mode,
		    ],
		    ['name' => 'test', 'type' => 'hidden', 'value' => $test],
		    ['name' => 'step-texts', 'type' => 'hidden', 'value' => true],
		];
		if (!isset($heap['step-texts']) && $mode == config('global.create')) {
		    $custom = false;
		    $fields[] = [
		        'name' => 'pretext',
		        'title' => 'Вводный текст на первом экране теста',
		        'required' => false,
		        'type' => 'text',
		    ];
		    $fields[] = [
		        'name' => 'posttext',
		        'title' => 'Финальный текст на последнем экране теста (если нет показа результата теста)',
		        'required' => false,
		        'type' => 'text',
		    ];
		} else {
		    $custom = $options & \App\Models\TestOptions::CUSTOM_TEXTS->value;
		    $fields[] = [
		        'name' => 'pretext',
		        'title' => 'Вводный текст на первом экране теста',
		        'required' => false,
		        'type' => 'textarea',
		        'value' => $heap['texts']['pretext'] ?? '',
		    ];
		    $fields[] = [
		        'name' => 'posttext',
		        'title' => 'Финальный текст на последнем экране теста (если нет показа результата теста)',
		        'required' => false,
		        'type' => 'textarea',
		        'value' => $heap['texts']['posttext'] ?? '',
		    ];
		}
	@endphp
@endsection

@section('form.before.content')
	<div class="col-sm-8 mb-4 p-4">
		<div class="form-check form-switch">
			<input class="form-check-input" type="checkbox" id="texts-option" name="texts-option"
				@if ($custom) checked @endif @if ($mode == config('global.show')) disabled @endif>
			<label class="form-check-label" for="texts-option">Тест включает настраиваемые тексты на страницах</label>
			{{-- {{ json_encode($heap) }}
			{{ $mode == config('global.create') ? 'create' : 'edit' }} --}}
		</div>
	</div>
@endsection

@push('js_after')
	<script>
		document.getElementById('texts-option').addEventListener('change', (event) => {
			if (event.target.checked) {
				document.getElementById('content').style.display = 'block';
			} else {
				document.getElementById('content').style.display = 'none';
			}
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('texts-option').dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
