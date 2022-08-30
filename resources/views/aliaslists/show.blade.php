@extends('layouts.detail')

@section('service')
	Работа с ссылочными блоками
@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		if ($parent) {
            $steps = [
				['title' => 'Ссылочный блок', 'active' => false, 'context' => 'alias', 'link' => route('aliaslists.index', ['sid' => session()->getId()])],
				['title' => 'Блок-предок', 'active' => true, 'context' => 'parent'],
			];
        } else {
			$steps = [
				['title' => 'Ссылочный блок', 'active' => true, 'context' => 'alias', 'link' => route('aliaslists.index', ['sid' => session()->getId()])],
				['title' => 'Блок-предок', 'active' => false, 'context' => 'parent'],
			];
        }
	@endphp
@endsection

@section('interior.header')
	Просмотр
	@if ($parent)
		блока-предка
	@else
		ссылочного блока
	@endif
@endsection

@section('form.params')
	id="aliaslist-show" name="aliaslist-show"
	action=""
@endsection

@section('form.fields')
	@php
		$fields = [
            ['name' => 'id', 'title' => 'ID блока', 'required' => true, 'type' => 'text', 'value' => $block->getKey()],
            ['name' => 'fmptype', 'title' => 'Тип описания блока', 'required' => false, 'type' => 'text', 'value' => $block->profile->fmptype->name],
            ['name' => 'profile', 'title' => 'Нейропрофиль блока', 'required' => false, 'type' => 'text', 'value' => $block->profile->name],
			['name' => 'name', 'title' => 'Название блока', 'required' => true, 'type' => 'text', 'value' => $block->name],
		];
        switch ($block->type) {
            case App\Models\BlockType::Text->value:
                $fields[] = ['name' => 'short', 'title' => 'Краткий текст блока', 'required' => false, 'type' => 'textarea', 'value' => $block->short];
                $fields[] = ['name' => 'full', 'title' => 'Полный текст блока', 'required' => false, 'type' => 'editor', 'value' => $block->full];
                break;
			case \App\Models\BlockType::Alias->value:
				$fields[] = ['type' => 'heading', 'title' => 'Данные блока-предка ссылочного блока'];
				$fields[] = ['name' => 'id', 'title' => 'ID блока-предка', 'required' => false, 'type' => 'text', 'value' => $block->parent->getKey(), 'disabled' => true];
				$fields[] = ['name' => 'pname', 'title' => 'Название блока-предка', 'required' => false, 'type' => 'text', 'value' => $block->parent->name, 'disabled' => true];
				$fields[] = ['name' => 'short', 'title' => 'Краткий текст блока-предка', 'required' => false, 'type' => 'textarea', 'value' => $block->parent->short, 'disabled' => true];
				$fields[] = ['name' => 'full', 'title' => 'Полный текст блока-предка', 'required' => false, 'type' => 'editor', 'value' => $block->parent->full, 'disabled' => true];
				break;
        };
	@endphp
@endsection

@section('form.close')
	{{ route('aliaslists.index', ['sid' => session()->getId()]) }}
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
