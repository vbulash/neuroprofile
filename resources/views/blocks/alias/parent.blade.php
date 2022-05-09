@extends('layouts.detail')

@section('service')Работа с описаниями результатов тестирования@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index', ['sid' => session()->getId()])],
			['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index', ['sid' => session()->getId()])],
			['title' => 'Блок описания', 'active' => true, 'context' => 'block', 'link' => route('blocks.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Просмотр родительского блока описания &laquo;{{ $block->name }}&raquo;<br/>
	<small>Родительский блок используется как основа для создания ссылочного блока</small>
@endsection

@section('form.params')
	id="parent-show" name="parent-show"
	action=""
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'name', 'title' => 'Название блока', 'required' => true, 'type' => 'text', 'value' => $block->name],
			['name' => 'short', 'title' => 'Краткий текст блока', 'required' => false, 'type' => 'textarea', 'value' => $block->short],
			['name' => 'full', 'title' => 'Полный текст блока', 'required' => false, 'type' => 'editor', 'value' => $block->full],
		];
	@endphp
@endsection

@section('form.close')
	{{ route('aliases.index', ['sid' => session()->getId()]) }}
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
				editor.isReadOnly = true;
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});
	</script>
@endpush
