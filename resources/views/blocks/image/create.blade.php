@extends('layouts.detail')

@section('service')Работа с описаниями результатов тестирования@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Тип описания', 'active' => false, 'context' => 'fmptype', 'link' => route('fmptypes.index')],
			['title' => 'Нейропрофиль', 'active' => false, 'context' => 'profile', 'link' => route('profiles.index')],
			['title' => 'Блок описания', 'active' => true, 'context' => 'block', 'link' => route('blocks.index')],
		];
	@endphp
@endsection

@section('interior.header')
	Новый блок-изображение
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
			['name' => 'full', 'title' => 'Изображение блока', 'required' => true, 'type' => 'image'],
			['name' => 'type', 'type' => 'hidden', 'value' => \App\Models\BlockType::Image->value],
			['name' => 'profile_id', 'type' => 'hidden', 'value' => $profile->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Block::class, $mode, 'close') }}
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
