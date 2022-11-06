@extends('layouts.detail')

@section('service')Работа с вопросами тестирования@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Набор вопросов', 'active' => false, 'context' => 'set', 'link' => route('sets.index')],
			['title' => 'Вопросы', 'active' => false, 'context' => 'question', 'link' => route('questions.index')],
			['title' => 'Изображения вопросов', 'active' => true, 'context' => 'part', 'link' => route('parts.index')]
		];
	@endphp
@endsection

@section('interior.header')
	Новое изображение вопроса № {{ $question->getTitle() }}
@endsection

@section('form.params')
	id="{{ form(\App\Models\Part::class, $mode, 'id') }}" name="{{ form(\App\Models\Part::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Part::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'image', 'title' => 'Изображение', 'required' => true, 'type' => 'image'];
		$fields[] = ['name' => 'key', 'title' => 'Ключ изображения', 'required' => false, 'type' => 'select', 'options' => $keys];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Part::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		const field = 'image';

		function readImage(input) {
			if (input.files && input.files[0]) {
				window.preview = 'preview_' + field;
				window.clear = 'clear_' + field;

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
				let image = 'preview_' + field;
				let source = document.getElementById(image).dataset.origin;

				let file = document.getElementById(event.target.dataset.image);
				file.setAttribute('type', 'text');
				file.setAttribute('type', 'file');

				document.getElementById(image).setAttribute('src', source);
				event.target.style.display = 'none';
			});
		});

		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('clear_' + field).style.display = 'none';
		}, false);
	</script>
@endpush
