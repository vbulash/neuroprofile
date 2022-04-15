@extends('layouts.wizard')

@section('service')Работа с вопросами тестирования@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Набор вопросов', 'active' => true, 'context' => 'set', 'link' => route('sets.index', ['sid' => session()->getId()])],
			['title' => 'Вопросы', 'active' => false, 'context' => 'question'],
		];
	@endphp
@endsection

@section('interior')
	<form role="form" class="p-5" method="post"
		  id="set-edit" name="set-edit"
		  action="{{ route('sets.update', ['set' => $set->getKey(), 'sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@method('PUT')
		<div class="block-header block-header-default">
			<h3 class="block-title fw-semibold">
				@if($show) Просмотр @else Редактирование @endif набора вопросов
				@if(!$show)
					<br/>
					<small><span class="required">*</span> - поля, обязательные для заполнения</small>
				@endif
			</h3>
		</div>
		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'name', 'title' => 'Наименование набора вопросов', 'required' => true, 'type' => 'text', 'value' => $set->name],
					['name' => 'code', 'title' => 'PHP-код вычисления кода нейропрофиля', 'required' => true, 'type' => 'editor', 'value' => $set->code],
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">
						{{ $field['title'] }} @if($field['required'] && !$show) <span class="required">*</span> @endif
					</label>
					@switch($field['type'])

						@case('text')
						@case('email')
						@case('number')
						<div class="col-sm-5">
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ old($field['name'], $field['value']) }}"
								   @if($show) disabled @endif
							>
						</div>
						@break

						@case('editor')
						<div class="col-sm-9">
							@if(!$show)
								<input type="hidden" name="{{ $field['name'] }}" id="{{ $field['name'] }}">
							@endif
							<div class="row">
								<div class="document-editor__toolbar"></div>
							</div>
							<div class="row row-editor">
								<div class="editor">{!! old($field['name'], $field['value']) !!}</div>
							</div>
						</div>
						@break;
					@endswitch
				</div>
			@endforeach
		</div>

		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				<div class="col-sm-5">
					@if($show)
						<a class="btn btn-primary pl-3"
						   href="{{ route('sets.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@else
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('sets.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@endif
				</div>
			</div>
		</div>
	</form>
@endsection

@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/ckeditor.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/ckeditor.js') }}"></script>
	<script>
		DecoupledDocumentEditor
			.create(document.querySelector('.editor'), {
				toolbar: {
					items: [
						'heading',
						'|',
						'fontSize',
						'fontFamily',
						'|',
						'fontColor',
						'fontBackgroundColor',
						'|',
						'bold',
						'italic',
						'underline',
						'strikethrough',
						'subscript',
						'superscript',
						'highlight',
						'|',
						'alignment',
						'|',
						'numberedList',
						'bulletedList',
						'|',
						'outdent',
						'indent',
						'codeBlock',
						'|',
						'todoList',
						'link',
						'blockQuote',
						'insertTable',
						'|',
						'undo',
						'redo'
					]
				},
				language: 'ru',
				codeBlock: {
					languages: [
						{language: 'php', label: 'PHP'}
					]
				},
				table: {
					contentToolbar: [
						'tableColumn',
						'tableRow',
						'mergeTableCells',
						'tableCellProperties',
						'tableProperties'
					]
				},
				licenseKey: '',
			})
			.then(editor => {
				window.editor = editor;
				document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
				document.querySelector('.ck-toolbar').classList.add('ck-reset_all');
				@if($show)
					editor.isReadOnly = true;
				@endif
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		@if(!$show)
			document.getElementById('set-edit').addEventListener('submit', () => {
				document.getElementById('code').value = editor.getData();
			}, false);
		@endif
	</script>
@endpush
