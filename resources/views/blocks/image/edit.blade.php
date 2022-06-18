@extends('layouts.detail')

@section('service')Работа с описаниями результатов тестирования@endsection

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
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif блока-изображения &laquo;{{ $block->name }}&raquo;
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
			['name' => 'full', 'title' => 'Изображение блока', 'required' => true, 'type' => 'image', 'value' => $block->full],
			['name' => 'type', 'type' => 'hidden', 'value' => $block->type],
			['name' => 'profile_id', 'type' => 'hidden', 'value' => $block->profile->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($block, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		function readImage(input) {
			if (input.files && input.files[0]) {
				window.preview = 'preview_full';
				window.clear = 'clear_full';

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
