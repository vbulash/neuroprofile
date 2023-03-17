@extends('tests.steps.wizard')

@section('service')
	@if ($mode == config('global.create'))
		Создание теста
	@else
		@php
			$heap = session('heap');
		@endphp
		@if ($mode == config('global.show'))
			Просмотр
		@else
			Редактирование
		@endif теста &laquo;{{ $heap['name'] }}&raquo;
	@endif
@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('interior.subheader')
@endsection

@section('form.fields')
	@php
		$heap = session('heap');
		$fields = [['name' => 'mode', 'type' => 'hidden', 'value' => $mode], ['name' => 'test', 'type' => 'hidden', 'value' => $test], ['name' => 'step-branding', 'type' => 'hidden', 'value' => true]];
	@endphp
@endsection

@section('form.method')
	method="post"
@endsection
@section('form.put')
@endsection {{-- Для финального шага = POST --}}

@section('form.before.content')
	@php
		$heap = session('heap');
		$options = intval($heap['options'] ?? 0);
		$defaultSignature = sprintf("<p style='margin-top: 40px;'>\n" . "С уважением,<br/>\n" . "<a href=\"%s\" target=\"_blank\">%s</a>\n" . "</p>\n", env('BRAND_URL'), env('BRAND_NAME'));
		if (!isset($heap['step-branding']) && $mode == config('global.create')) {
		    $custom = false;
		    $logo = '';
		    $background = '#007bff';
		    $color = '#ffffff';
		    $company = env('APP_NAME');
		    $signature = $defaultSignature;
		} else {
		    $custom = $options & \App\Models\TestOptions::CUSTOM_BRANDING->value;
		    $logo = isset($heap['branding']['logo']) ? '/uploads/' . $heap['branding']['logo'] : '';
		    $background = $heap['branding']['background'] ?? '#007bff';
		    $color = $heap['branding']['fontcolor'] ?? '#ffffff';
		    $company = $heap['branding']['company-name'] ?? env('APP_NAME');
		    $signature = $heap['branding']['signature'] ?? $defaultSignature;
		}
	@endphp
	<div class="col-sm-8 mb-4 p-4">
		<div class="form-check form-switch">
			<input class="form-check-input" type="checkbox" id="branding-option" name="branding-option"
				@if ($custom) checked @endif @if ($mode == config('global.show')) disabled @endif>
			<label class="form-check-label" for="branding-option">Тест имеет самостоятельный брендинг, отличный от
				встроенного</label>
		</div>
	</div>
	<div class="p-4" id="branding-panel" style="display: none">
		<div class="row mb-4">
			<div class="col-md-6">
				<div class="form-group">
					<label for="logo-file">Логотип:</label>
					<input type="file" id="logo-file" name="logo-file" class="image-file mb-4 form-control"
						@if ($mode == config('global.show')) disabled @endif onchange="readLogoImage(this)">
					<a href="javascript:void(0)" class="preview_anchor" data-toggle="lightbox" data-title="Логотип">
						<img id="preview_logo-file" src="{{ $logo }}" alt="" class="col-sm-6 mb-2">
					</a>
					<a href="javascript:void(0)" id="clear_logo-file" data-preview="preview_logo-file"
						class="btn btn-primary mb-4 col-sm-6">Очистить</a>
				</div>
			</div>
		</div>
		<div class="row mb-4">
			<div class="col-md-3">
				<div class="form-group">
					<div class="input-group pl-0">
						<label for="background" class="form-control pl-0" style="border: none">Первичный цвет:</label>
						<div class="input-group-append">
							<div id="back-picker"></div>
							<input type="hidden" name="background-input" id="background-input" />
						</div>
					</div>
					<div class="input-group pl-0">
						<label for="font-color" class="form-control pl-0" style="border: none">Цвет шрифта:</label>
						<div class="input-group-append">
							<div id="font-picker"></div>
							<input type="hidden" name="font-color-input" id="font-color-input" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row mb-4">
			<div class="form-group">
				<label for="signature_editor">Подпись в письме с результатами тестирования:</label>
				<input type="hidden" id="signature" name="signature">
				<div class="col-sm-9">
					<div class="row">
						<div class="document-editor__toolbar"></div>
					</div>
					<div class="row row-editor">
						<div class="editor" id="signature_editor">{!! $signature !!}</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="company-name-changer">Организация / компания:</label>
			<input type="text" name="company-name-changer" id="company-name-changer" class="form-control"
				@if ($mode == config('global.show')) disabled @endif value="{{ $company }}">
		</div>

		<div class="preview mt-5">
			<div class="form-group">
				<label for="preview_nav">Предпросмотр заголовка окна / фрейма:</label>
				<nav class="navbar navbar-dark d-none d-lg-flex align-content-center custom-background mb-2" id="preview_nav">
					<div class="">
						<span id="preview_logo" style="height: 20px;"></span>
						<span id="company-name-demo">{{ $company }}</span>
					</div>
					<div class="">
						Наименование теста
					</div>
				</nav>
			</div>

			<label for="preview_button">Предпросмотр кнопки:</label><br />
			<a href="javascript:void(0)" class="btn ml-0 custom-background custom-color" id="preview_button">Начать
				тестирование</a>
		</div>
	</div>
@endsection

@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/classic.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/ckeditor.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/pickr.min.js') }}"></script>
	<script src="{{ asset('js/ckeditor.js') }}"></script>
	<script>
		function readLogoImage(input) {
			if (input.files && input.files[0]) {
				let reader = new FileReader();
				reader.onload = function(event) {
					document.getElementById('preview_logo-file').setAttribute('src', event.target.result);
					document.getElementById('preview_logo-file').style.display = 'block';
					document.getElementById('clear_logo-file').style.display = 'block';
					document.getElementById('preview_logo').innerHTML =
						"<img src=\"" + event.target.result + "\" class=\"preview_logo\" style=\"height: 20px;\">";
				};
				reader.readAsDataURL(input.files[0]);
			}
		}

		function updateTextPreview(textColor, backColor) {
			document.getElementById('preview_nav').style.setProperty('background-color', backColor, 'important');
			document.getElementById('preview_nav').style.setProperty('color', textColor, 'important');
			document.getElementById('preview_button').setAttribute('style',
				'background-color: ' + backColor + ' !important;' +
				'border-color: ' + backColor + ' !important;' +
				'color: ' + textColor + ' !important'
			);
		}

		let backgroundColor = "{{ $background }}";
		let fontColor = "{{ $color }}";

		document.getElementById('branding-option').addEventListener('change', (event) => {
			if (event.target.checked) {
				document.getElementById('branding-panel').style.display = 'block';
				document.getElementById('clear_logo-file').style.display = 'none';
				document.getElementById('font-color-input').value = fontColor;
				document.getElementById('background-input').value = backgroundColor;
				document.getElementById('company-name-changer').dispatchEvent(new Event('input'));
			} else {
				document.getElementById('branding-panel').style.display = 'none';
			}
		}, false);

		document.getElementById('company-name-changer').addEventListener('input', (event) => {
			let company = event.target;
			document.getElementById('company-name-demo').innerText = company.value;
		}, false);

		document.getElementById('clear_logo-file').addEventListener('click', () => {
			let file = document.getElementById('logo-file');
			file.setAttribute('type', 'text');
			file.setAttribute('type', 'file');

			document.getElementById('preview_logo-file').style.display = 'none';
			document.getElementById('clear_logo-file').style.display = 'none';
			document.getElementById('preview_logo').innerHTML = "<i class=\"fas fa-home\"></i>";
		});

		document.addEventListener("DOMContentLoaded", () => {
			@if ($logo)
				document.getElementById('clear_logo-file').style.display =
					@if ($mode == config('global.show'))
						'none';
					@else
						'block';
					@endif
				document.getElementById('preview_logo').innerHTML =
					"<img src=\"{{ $logo }}\" class=\"preview_logo\" style=\"height: 20px;\">";
			@else
				document.getElementById('clear_logo-file').click();
			@endif
			if (document.getElementById('branding-option').checked) {
				document.getElementById('branding-panel').style.display = 'block';
				updateTextPreview(fontColor, backgroundColor);
			} else {
				document.getElementById('branding-panel').style.display = 'none';
			}

			let pickrOptions = {
				el: '',
				theme: 'classic',

				default: '',

				swatches: [
					'rgba(244, 67, 54, 1)',
					'rgba(233, 30, 99, 1)',
					'rgba(156, 39, 176, 1)',
					'rgba(103, 58, 183, 1)',
					'rgba(63, 81, 181, 1)',
					'rgba(33, 150, 243, 1)',
					'rgba(3, 169, 244, 1)',
					'rgba(0, 188, 212, 1)',
					'rgba(0, 150, 136, 1)',
					'rgba(76, 175, 80, 1)',
					'rgba(139, 195, 74, 1)',
					'rgba(205, 220, 57, 1)',
					'rgba(255, 235, 59, 1)',
					'rgba(255, 193, 7, 1)'
				],

				i18n: {
					'btn:save': 'Сохранить',
					'btn:cancel': 'Отменить',
					'btn:clear': 'Очистить',
				},

				components: {
					preview: true,
					opacity: false,
					hue: false,

					interaction: {
						hex: false,
						rgba: false,
						hsla: false,
						hsva: false,
						cmyk: false,
						input: true,
						clear: false,
						save: true
					}
				}
			};

			let backPickrOptions = pickrOptions;
			backPickrOptions.el = '#back-picker';
			backPickrOptions.default = backgroundColor;

			const backgroundColorPickr = Pickr.create(backPickrOptions);

			backgroundColorPickr
				.on('save', instance => {
					let selectedColor = instance.toHEXA().toString();
					document.querySelectorAll('.custom-color').forEach(element => {
						element.style.setProperty('background-color', selectedColor, 'important');
					});
					updateTextPreview(fontColor, selectedColor);
					document.getElementById('background-input').value = selectedColor;
					backgroundColor = selectedColor;
				});

			let fontPickrOptions = pickrOptions;
			fontPickrOptions.el = '#font-picker';
			fontPickrOptions.default = fontColor;

			const fontColorPickr = Pickr.create(fontPickrOptions);
			fontColorPickr
				.on('save', instance => {
					let selectedColor = instance.toHEXA().toString();
					document.querySelectorAll('.custom-color').forEach(element => {
						element.style.setProperty('color', selectedColor, 'important');
					});
					updateTextPreview(selectedColor, backgroundColor);
					document.getElementById('font-color-input').value = selectedColor;
					fontColor = selectedColor;
				});

			@if ($mode == config('global.show'))
				document.querySelectorAll('.pickr button').forEach((button) => {
					button.disabled = true;
				});
			@endif
		}, false);

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

		document.getElementById('core-create').addEventListener('submit', () => {
			document.getElementById('signature').value = editor.getData();
		}, false);
	</script>
@endpush
