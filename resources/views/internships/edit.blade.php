@extends('layouts.backend')

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@if($show) Просмотр @else Редактирование @endif
					стажировки у работодателя &laquo;{{ $internship->employer->name }}&raquo;</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Лица</li>
						<li class="breadcrumb-item">Работодатели</li>
						<li class="breadcrumb-item active" aria-current="page">Стажировки</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<div class="block block-rounded">
			@if(!$show)
				<div class="block-header-default">
					<div class="row pt-5 ps-5 pe-5">
						<p><span class="required">*</span> - поля, обязательные для заполнения</p>
					</div>
				</div>
			@endif
			<form role="form" class="p-5" method="post"
				  id="internship-edit" name="internship-edit"
				  action="{{ route('internships.update', ['internship' => $internship->getKey(), 'sid' => session()->getId()]) }}"
				  autocomplete="off" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="iname">Название стажировки @if(!$show) <span
							class="required">*</span>@endif</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="iname" name="iname" value="{{ $internship->iname }}" @if($show) disabled @endif>
					</div>
				</div>

				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="itype">Тип стажировки @if(!$show) <span
							class="required">*</span>@endif</label>
					<div class="col-sm-5">
						@if($show)
							<input type="text" class="form-control" name="itype" id="itype" value="{{ $internship->itype }}" disabled>
						@else
							@php
								$options = [
									'Открытая стажировка' => 'Открытая стажировка (практикант может записаться самостоятельно)',
									'Закрытая стажировка' => 'Закрытая стажировка (практикантов выбирает работодатель)'
								];
							@endphp
							<select class="form-control select2" name="itype" id="itype">
								@foreach($options as $key => $value)
									<option value="{{ $key }}"
											@if($internship->itype == $key) selected @endif>{{ $value }}
									</option>
								@endforeach
							</select>
						@endif
					</div>
				</div>

				<div class="row mb-4">
					<input type="hidden" name="status" id="status" value="Планируется">
					<label class="col-sm-3 col-form-label" for="status">Статус стажировки @if(!$show) <span
							class="required">*</span>@endif</label>
					<div class="col-sm-5">
						@if($show)
							<input type="text" class="form-control" name="status" id="status" value="{{ $internship->status }}" disabled>
						@else
							@php
								$options = [
									'Планируется' => 'Планируется (нет практикантов)',
									'Выполняется' => 'Выполняется (есть назначенные практиканты)',
									'Закрыта' => 'Завершена',
								];
							@endphp
							<select class="form-control select2" name="status" id="status">
								@foreach($options as $key => $value)
									<option value="{{ $key }}"
											@if($internship->status == $key) selected @endif>{{ $value }}
									</option>
								@endforeach
							</select>
						@endif
					</div>
				</div>

				<div class="row mb-4">
					<input type="hidden" name="program" id="program">
					<label class="col-sm-3 col-form-label" for="content">Программа стажировки @if(!$show) <span
							class="required">*</span>@endif</label>
					<div class="col-sm-9">
						<div class="row">
							<div class="document-editor__toolbar"></div>
						</div>
						<div class="row row-editor">
							<div class="editor">{!! $internship->program !!}</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-3 col-form-label">&nbsp;</div>
					<div class="col-sm-5">
						@if($show)
							<a class="btn btn-primary pl-3"
							   href="{{ route('internships.index', ['employer' => $internship->employer->getKey(), 'sid' => session()->getId()]) }}"
							   role="button">Закрыть</a>
						@else
							<button type="submit" class="btn btn-primary">Сохранить</button>
							<a class="btn btn-secondary pl-3"
							   href="{{ route('internships.index', ['employer' => $internship->employer->getKey(), 'sid' => session()->getId()]) }}"
							   role="button">Закрыть</a>
						@endif
					</div>
				</div>
			</form>
		</div>
	</div>
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

				@if($show) editor.isReadOnly = true; @endif
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		document.getElementById('internship-edit').addEventListener('submit', () => {
			document.getElementById('program').value = editor.getData();
		}, false);
	</script>
@endpush
