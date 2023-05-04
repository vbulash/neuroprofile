@extends('blocks.edit', ['prev' => $prev, 'next' => $next, 'form' => form($block, $mode, 'name')])

@section('service')
	@switch($kind)
		@case(\App\Models\BlockKind::Parent->value)
		@case(\App\Models\BlockKind::Kid->value)
			Работа с ссылочными блоками
			@break
		@case(\App\Models\BlockKind::Block->value)
			Работа с описаниями результатов тестирования
			@break
	@endswitch
@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		$steps = match (strval($kind)) {
            \App\Models\BlockKind::Parent->value,
            \App\Models\BlockKind::Kid->value => [
                ['title' => 'Блок-предок', 'active' => true, 'context' => 'parent', 'link' => route('parents.index')],
				['title' => 'Блок-потомок', 'active' => false, 'context' => 'profile', 'link' => '#'],
			],
			\App\Models\BlockKind::Block->value => [
				['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index')],
				['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index')],
				['title' => 'Блок описания', 'active' => true, 'context' => 'block', 'link' => route('blocks.index')],
			]
		};
        $close = match (strval($kind)) {
            \App\Models\BlockKind::Parent->value => route('parents.index'),
            \App\Models\BlockKind::Block->value => form($block, $mode, 'close'),
            //\App\Models\BlockKind::Kid->value =>
        };
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif блока описания &laquo;{{ $block->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($block, $mode, 'id') }}" name="{{ form($block, $mode, 'name') }}"
	action="{{ form($block, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'name', 'title' => 'Название блока', 'required' => true, 'type' => 'text', 'value' => $block->name],
			['name' => 'short', 'title' => 'Краткий текст блока', 'required' => false, 'type' => 'textarea', 'value' => $block->short],
			['name' => 'full', 'title' => 'Полный текст блока', 'required' => false, 'type' => 'editor', 'value' => $block->full],
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
