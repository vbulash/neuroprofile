@extends('layouts.detail')

@section('service')Работа с описаниями результатов тестирования@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		$mode = config('global.create');
		$steps = [
			['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index', ['sid' => session()->getId()])],
			['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index', ['sid' => session()->getId()])],
			['title' => 'Блок описания', 'active' => true, 'context' => 'block', 'link' => route('blocks.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новый ссылочный блок описания
@endsection

@section('form.params')
	id="{{ form(\App\Models\Block::class, $mode, 'id') }}" name="{{ form(\App\Models\Block::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Block::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$parent = \App\Models\Block::findOrFail($block_id);
		$fields = [
			['name' => 'name', 'title' => 'Название ссылочного блока', 'required' => true, 'type' => 'text'],
			['name' => 'block_id', 'type' => 'hidden', 'value' => $block_id],
			['name' => 'type', 'type' => 'hidden', 'value' => \App\Models\BlockType::Alias->value],
			['name' => 'profile_id', 'type' => 'hidden', 'value' => $profile_id],
			['type' => 'heading', 'title' => 'Данные блока-предка нового ссылочного блока'],
			['name' => 'id', 'title' => 'ID блока-предка', 'required' => false, 'type' => 'text', 'value' => $parent->getKey(), 'disabled' => true],
			['name' => 'pname', 'title' => 'Название блока-предка', 'required' => false, 'type' => 'text', 'value' => $parent->name, 'disabled' => true],
			['name' => 'short', 'title' => 'Краткий текст блока-предка', 'required' => false, 'type' => 'textarea', 'value' => $parent->short, 'disabled' => true],
			['name' => 'full', 'title' => 'Полный текст блока-предка', 'required' => false, 'type' => 'editor', 'value' => $parent->full, 'disabled' => true],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Block::class, $mode, 'close') }}
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
