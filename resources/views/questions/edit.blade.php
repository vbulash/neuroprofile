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
		  id="client-edit" name="client-edit"
		  action="{{ route('questions.update', ['question' => $question->getKey(), 'sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@method('PUT')
		<div class="block-header block-header-default">
			<h3 class="block-title fw-semibold">
				@if($show)
					Просмотр
				@else
					Редактирование
				@endif вопроса № {{ $question->sort_no }} для набора вопросов &laquo;{{ $question->set->name }}&raquo;.
				@if(!$show)
					<br/>
					<small><span class="required">*</span> - поля, обязательные для заполнения</small>
				@endif
			</h3>
		</div>
		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'learning', 'title' => 'Режим прохождения', 'required' => true, 'type' => 'select', 'options' => [
                        '0' => 'Реальный вопрос',
                        '1' => 'Учебный вопрос'
					], 'value' => ($question->learning ? 1 : 0)],
					['name' => 'timeout', 'title' => 'Таймаут прохождения вопроса, секунд', 'required' => true, 'type' => 'number', 'value' => $question->timeout],
					//
					['name' => 'value1', 'title' => 'Заглушка ключа 1', 'required' => true, 'type' => 'text', 'value' => $question->value1],
					['name' => 'value2', 'title' => 'Заглушка ключа 2', 'required' => true, 'type' => 'text', 'value' => $question->value2],
					['name' => 'image1', 'type' => 'hidden', 'value' => 'x'],
					['name' => 'image2', 'type' => 'hidden', 'value' => 'x'],
					//
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					@switch($field['type'])
						@case('hidden')
						@break

						@default
						<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
							@if($field['required'] || !$show)
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
								   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
								   @if($show) disabled @endif
							>
						</div>
						@break

						@case('textarea')
						<div class="col-sm-5">
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30"
									  rows="5"
									  @if($show) disabled @endif
							>{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}</textarea>
						</div>
						@break

						@case('select')
						<div class="col-sm-5">
							<select class="form-control select2" name="{{ $field['name'] }}"
									id="{{ $field['name'] }}" @if($show) disabled @endif>
								@foreach($field['options'] as $key => $value)
									<option value="{{ $key }}"
											@if($field['value'] == $key) selected @endif>{{ $value }}</option>
								@endforeach
							</select>
						</div>
						@break

						@case('hidden')
						<input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}" value="{{ $field['value'] }}">
						@break
					@endswitch
				</div>
			@endforeach
		</div>

		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				@if($show)
					<div class="col-sm-5">
						<a class="btn btn-primary pl-3"
						   href="{{ route('questions.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					</div>
				@else
					<div class="col-sm-5">
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('questions.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					</div>
				@endif
			</div>
		</div>
	</form>
@endsection
