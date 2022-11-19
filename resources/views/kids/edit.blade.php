@extends('layouts.detail')

@section('service')
	Работа с ссылочными блоками
@endsection

@section('steps')
	@php
		$steps = [
            ['title' => 'Блок-предок', 'active' => false, 'context' => 'parent', 'link' => route('parents.index')],
			['title' => 'Блок-потомок', 'active' => true, 'context' => 'profile', 'link' => route('kids.index')],
		];
        $close = route('kids.index');
	@endphp
@endsection

@section('interior.header')
	Редактирование блока-потомка &laquo;{{ $block->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($block, $mode, 'id') }}" name="{{ form($block, $mode, 'name') }}"
	action="{{ form($block, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'name', 'title' => 'Название ссылочного блока', 'required' => true, 'type' => 'text', 'value' => $block->name],
			['name' => 'kind', 'type' => 'hidden', 'value' => $kind],
			['name' => 'type', 'type' => 'hidden', 'value' => $block->type],
			['name' => 'profile_id', 'type' => 'hidden', 'value' => $block->profile->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ $close }}
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
				@if($mode == config('global.show'))
					editor.isReadOnly = true;
				@endif
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		@if($mode != config('global.show'))
		document.getElementById('block-edit').addEventListener('submit', () => {
			document.getElementById('full').value = editor.getData();
		}, false);
		@endif
	</script>
@endpush
