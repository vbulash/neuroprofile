@extends('layouts.wizard')

@section('service')
	Работа с вопросами тестирования
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Набор вопросов', 'active' => false, 'context' => 'set', 'link' => route('sets.index', ['sid' => session()->getId()])],
			['title' => 'Вопросы', 'active' => true, 'context' => 'question', 'link' => route('questions.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior')
	<form role="form" class="p-5" method="post"
		  id="client-create" name="client-create"
		  action="{{ route('questions.store', ['sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		<div class="block-header block-header-default">
			<h3 class="block-title fw-semibold">
				Создание вопроса для набора вопросов &laquo;{{ $set->name }}&raquo;.<br/>
				<small><span class="required">*</span> - поля, обязательные для заполнения</small>
			</h3>
		</div>
		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'learning', 'title' => 'Режим прохождения', 'required' => true, 'type' => 'select', 'options' => [
                        '0' => 'Реальный вопрос',
                        '1' => 'Учебный вопрос'
					]],
					['name' => 'timeout', 'title' => 'Таймаут прохождения вопроса, секунд', 'required' => true, 'type' => 'number', 'value' => 0],
					//
					['name' => 'image1', 'type' => 'hidden', 'value' => 'x'],
					['name' => 'image2', 'type' => 'hidden', 'value' => 'x'],
					//
					['name' => 'set_id', 'type' => 'hidden', 'value' => $set->getKey()],
				];
			@endphp

			@foreach($fields as $field)
				@switch($field['type'])
					@case('hidden')
					@break

					@default
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
							@if($field['required'])
								<span class="required">*</span>
							@endif</label>
						@break
						@endswitch

						@switch($field['type'])

							@case('text')
							@case('email')
							@case('number')
							<div class="col-sm-5">
								<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}">
							</div>
							@break

							@case('textarea')
							<div class="col-sm-5">
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30" rows="5">{{ old($field['name']) }}</textarea>
							</div>
							@break

							@case('select')
							<div>
								<select class="form-control select2" name="{{ $field['name'] }}"
										id="{{ $field['name'] }}">
									@foreach($field['options'] as $key => $value)
										<option value="{{ $key }}">{{ $value }}</option>
									@endforeach
								</select>
							</div>
							@break

							@case('hidden')
							<input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ $field['value'] }}">
							@break
						@endswitch
						@switch($field['type'])
							@case('hidden')
							@break

							@default
							@break
						@endswitch

						@switch($field['type'])
							@case('hidden')
							@break

							@default
					</div>
					@break
				@endswitch
			@endforeach

			@php
				$fields = [
					//
					['name' => 'value1', 'title' => 'Заглушка ключа 1', 'required' => true, 'type' => 'text'],
					['name' => 'value2', 'title' => 'Заглушка ключа 2', 'required' => true, 'type' => 'text'],
					//
				];
			@endphp

			<div class="row mb-4">
				@foreach($fields as $field)
					<div class="col-sm-6">
						<label class="col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
							@if($field['required'] || !$show)
								<span class="required">*</span>
							@endif
						</label>
						<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}"
							   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
						>
					</div>
				@endforeach
			</div>
		</div>

		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				<div class="col-sm-5">
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<a class="btn btn-secondary pl-3"
					   href="{{ route('questions.index', ['sid' => session()->getId()]) }}"
					   role="button">Закрыть</a>
				</div>
			</div>
		</div>
	</form>
@endsection
