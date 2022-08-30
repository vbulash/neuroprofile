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
	Новый текстовый блок описания
@endsection

@section('form.params')
	id="{{ form(\App\Models\Block::class, $mode, 'id') }}" name="{{ form(\App\Models\Block::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Block::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'name', 'title' => 'Название блока', 'required' => true, 'type' => 'text'],
			['name' => 'short', 'title' => 'Краткий текст блока', 'required' => false, 'type' => 'textarea'],
			['name' => 'full', 'title' => 'Полный текст блока', 'required' => false, 'type' => 'editor'],
			['name' => 'type', 'type' => 'hidden', 'value' => \App\Models\BlockType::Text->value],
			['name' => 'profile_id', 'type' => 'hidden', 'value' => $profile->getKey()],
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
	<script type="module">
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
						'|',
						'alignment',
						'|',
						'numberedList',
						'bulletedList',
						'|',
						'outdent',
						'indent',
						'|',
						'link',
						'blockQuote',
						'imageUpload',
						'insertTable',
						'mediaEmbed',
						'|',
						'undo',
						'redo'
					]
				},
				simpleUpload: {
					uploadUrl: 'http://example.com',
					withCredentials: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
				}
			})
			.then(editor => {
				//console.log(Array.from( editor.ui.componentFactory.names()));

				window.editor = editor;
				document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
				document.querySelector('.ck-toolbar').classList.add('ck-reset_all');
				//editor.isReadOnly = true;
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		document.getElementById('block-create').addEventListener('submit', () => {
			document.getElementById('full').value = editor.getData();
		}, false);
	</script>
@endpush
