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
				];
			@endphp

			@foreach($fields as $field)
				@switch($field['type'])
					@case('hidden')
					@break

					@default
					<div class="row mb-4">
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
					['name' => 'image1', 'title' => 'Левая картинка вопроса', 'type' => 'image', 'required' => true, 'value' => $question->image1],
					['name' => 'image2', 'title' => 'Правая картинка вопроса', 'type' => 'image', 'required' => true, 'value' => $question->image2],
					//
					['name' => 'value1', 'title' => 'Ключ левой картинки', 'required' => true, 'type' => 'select', 'value' => $question->value1, 'options' => \App\Models\Question::$values],
					['name' => 'value2', 'title' => 'Ключ правой картинки', 'required' => true, 'type' => 'select', 'value' => $question->value2, 'options' => \App\Models\Question::$values],
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
						@switch($field['type'])
							@case('text')
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}"
								   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
								   @if($show) disabled @endif
							>
							@break;

							@case('select')
							<select class="form-control select2" name="{{ $field['name'] }}"
									id="{{ $field['name'] }}" @if($show) disabled @endif>
								@foreach($field['options'] as $key)
									<option value="{{ $key }}"
											@if($field['value'] == $key) selected @endif>{{ $key }}</option>
								@endforeach
							</select>
							@break

							@case('image')
							<div class="row items-push mb-4">
								<input type="file" class="form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}"
									   onchange="readImage(this)"
									   @if($show) disabled @endif
								>
							</div>
							<div class="row mb-4" id="panel_{{ $field['name'] }}">
								<div class="col-sm-9">
									<img id="preview_{{ $field['name'] }}"
										 src="/uploads/{{ $field['value'] }}"
										 data-origin="/uploads/{{ $field['value'] }}"
										 alt=""
										 class="image-preview">
								</div>
								<div class="col-sm-3">
									<a class="btn btn-primary pl-3 @if($show) disabled @endif clear-preview"
									   href="javascript:void(0)"
									   id="clear_{{ $field['name'] }}"
									   data-image="{{ $field['name'] }}"
									   role="button"
									>Сбросить изменения</a>
								</div>
							</div>
							@break
						@endswitch
					</div>
				@endforeach
			</div>
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

@push('js_after')
	<script>
		function readImage(input) {
			if (input.files && input.files[0]) {
				window.preview = 'preview_' + input.id;
				window.clear = 'clear_' + input.id;

				let reader = new FileReader();
				reader.onload = function (event) {
					document.getElementById(window.preview).setAttribute('src', event.target.result);
					document.getElementById(window.clear).style.display = 'block';
				};
				reader.readAsDataURL(input.files[0]);
			}
		}

		document.querySelectorAll('.clear-preview').forEach(button => {
			document.getElementById(button.id).addEventListener('click', event => {
				let image = 'preview_' + event.target.dataset.image;
				let source = document.getElementById(image).dataset.origin;

				let file = document.getElementById(event.target.dataset.image);
				file.setAttribute('type', 'text');
				file.setAttribute('type', 'file');

				document.getElementById(image).setAttribute('src', source);
				event.target.style.display = 'none';
			});
		});

		document.addEventListener("DOMContentLoaded", () => {
			for (i = 1; i < 3; i++)
				document.getElementById('clear_image' + i).style.display = 'none';
		}, false);
	</script>
@endpush
