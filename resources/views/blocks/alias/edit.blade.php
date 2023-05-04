@extends('blocks.edit', ['prev' => $prev, 'next' => $next, 'form' => form($block, $mode, 'name')])

@section('service')
	Работа с описаниями результатов тестирования
@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		$steps = match (strval($kind)) {
		    \App\Models\BlockKind::Kid->value => [['title' => 'Блок-предок', 'active' => true, 'context' => 'parent', 'link' => route('parents.index')], ['title' => 'Блок-потомок', 'active' => false, 'context' => 'profile', 'link' => '#']],
		    \App\Models\BlockKind::Block->value => [['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index')], ['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index')], ['title' => 'Блок описания', 'active' => true, 'context' => 'block', 'link' => route('blocks.index')]],
		};
		$close = match (strval($kind)) {
		    \App\Models\BlockKind::Kid->value => route('kids.index'),
		    \App\Models\BlockKind::Block->value => form($block, $mode, 'close'),
		};
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif ссылочного блока описания &laquo;{{ $block->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($block, $mode, 'id') }}" name="{{ form($block, $mode, 'name') }}"
	action="{{ form($block, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		if ($kind == \App\Models\BlockKind::Block->value) {
		    $fields = [
		        ['name' => 'name', 'title' => 'Название ссылочного блока', 'required' => true, 'type' => 'text', 'value' => $block->name],
		        ['name' => 'block_id', 'type' => 'hidden', 'value' => $block->getKey()],
		        ['name' => 'type', 'type' => 'hidden', 'value' => \App\Models\BlockType::Alias->value],
		        ['name' => 'profile_id', 'type' => 'hidden', 'value' => $block->parent->getKey()],
		        ['type' => 'heading', 'title' => 'Данные блока-предка ссылочного блока'],
		        ['name' => 'id', 'title' => 'ID блока-предка', 'required' => false, 'type' => 'text', 'value' => $block->parent->getKey(), 'disabled' => true],
		        ['name' => 'pname', 'title' => 'Название блока-предка', 'required' => false, 'type' => 'text', 'value' => $block->parent->name, 'disabled' => true],
		        ['name' => 'short', 'title' => 'Краткий текст блока-предка', 'required' => false, 'type' => 'textarea', 'value' => $block->parent->short, 'disabled' => true],
		        ['name' => 'kind', 'type' => 'hidden', 'value' => $kind],
		        ['name' => 'type', 'type' => 'hidden', 'value' => $block->type],
		        ['name' => 'profile_id', 'type' => 'hidden', 'value' => $block->profile->getKey()],
		    ];
		    $fields[] = match ($block->parent->type) {
		        \App\Models\BlockType::Text->value => [
		            'name' => 'full',
		            'title' => 'Полный текст блока-предка',
		            'required' => false,
		            'type' => 'editor',
		            'value' => $block->parent->full,
		            'disabled' => true,
		        ],
		        \App\Models\BlockType::Image->value => [
		            'name' => 'full',
		            'title' => 'Изображение блока-предка',
		            'required' => false,
		            'type' => 'image',
		            'value' => $block->parent->full,
		            'disabled' => true,
		        ],
		    };
		} elseif ($kind == \App\Models\BlockKind::Kid->value) {
		    $fields = [['name' => 'name', 'title' => 'Название ссылочного блока', 'required' => true, 'type' => 'text', 'value' => $block->name], ['name' => 'kind', 'type' => 'hidden', 'value' => $kind], ['name' => 'type', 'type' => 'hidden', 'value' => $block->type], ['name' => 'profile_id', 'type' => 'hidden', 'value' => $block->profile->getKey()]];
		}
	@endphp
@endsection

@section('form.close')
	{{ form($block, $mode, 'close') }}
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
					languages: [{
						language: 'php',
						label: 'PHP'
					}]
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
				@if ($mode == config('global.show'))
					editor.isReadOnly = true;
				@endif
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error(
					'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:'
				);
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		@if ($mode != config('global.show'))
			document.getElementById('block-edit').addEventListener('submit', () => {
				document.getElementById('full').value = editor.getData();
			}, false);
		@endif

		function readImage(input) {
			if (input.files && input.files[0]) {
				window.preview = 'preview_full';
				window.clear = 'clear_full';

				let reader = new FileReader();
				reader.onload = function(event) {
					document.getElementById(window.preview).setAttribute('src', event.target.result);
					document.getElementById(window.clear).style.display = 'block';
				};
				reader.readAsDataURL(input.files[0]);
			}
		}

		document.querySelectorAll('.clear-preview').forEach(button => {
			document.getElementById(button.id).addEventListener('click', event => {
				let image = 'preview_full';
				let source = document.getElementById(image).dataset.origin;

				let file = document.getElementById(event.target.dataset.image);
				file.setAttribute('type', 'text');
				file.setAttribute('type', 'file');

				document.getElementById(image).setAttribute('src', source);
				event.target.style.display = 'none';
			});
		});

		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('clear_full').style.display = 'none';
		}, false);
	</script>
@endpush
