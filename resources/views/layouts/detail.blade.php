@extends('layouts.chain')

@section('buttons')
	<div class="col-sm-3 col-form-label">&nbsp;</div>
	<div class="col-sm-5">
		@if($mode == config('global.show'))
			<a class="btn btn-primary pl-3"
			   href="@yield('form.close')"
			   role="button">Закрыть</a>
		@else
			<button type="submit" class="btn btn-primary">Сохранить</button>
			<a class="btn btn-secondary pl-3"
			   href="@yield('form.close')"
			   role="button">Закрыть</a>
		@endif
	</div>
@endsection

@section('form.put')
@method('PUT')
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			@yield('interior.header')
			@if($mode != config('global.show'))
				<br/>
				<small><span class="required">*</span> - поля, обязательные для заполнения</small>
			@endif
		</h3>
	</div>
	<form role="form" method="post"
		  @yield('form.params')
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@if($mode == config('global.edit'))
			@yield('form.put')
		@endif

		<div class="block-content p-4 area">
			@yield('form.fields')

			@foreach($fields as $field)
				@switch($field['type'])
					@case('hidden')
					@break

					@case('heading')
					<div class="row mt-4 mb-4">
						<p><b>{{ $field['title'] }}</b></p>
						@break;

						@case('checkbox')
						<div class="row mb-4">
						@break

						@default
						<div class="row mb-4">
							<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
								@if($field['required'] && $mode != config('global.show'))
									<span class="required">*</span>
								@endif</label>
							@break
							@endswitch

							@switch($field['type'])

								@case('text')
								@case('email')
								@case('phone')
								@case('number')
								<div class="col-sm-5">
									<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
										   name="{{ $field['name'] }}"
										   @if($field['type'] == 'number' && isset($field['min']))
											   min="{{ $field['min'] }}"
										   @endif
										   @if($field['type'] == 'number' && isset($field['max']))
											   max="{{ $field['max'] }}"
										   @endif
										   autocomplete="off"
										   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
										   @if($mode == config('global.show') || isset($field['disabled'])) disabled @endif
									>
								</div>
								@break

								@case('checkbox')
									<div class="col-sm-8">
										<div class="form-check form-switch">
                  							<input class="form-check-input"
												type="checkbox"
												id="{{ $field['name'] }}" name="{{ $field['name'] }}"
												@if(isset($field['value']) && $field['value'])
													checked
												@endif
												@if($mode == config('global.show') || isset($field['disabled'])) disabled @endif>
                  							<label class="form-check-label" for="{{ $field['name'] }}">{{ $field['title'] }}</label>
                						</div>
									</div>
									@break

								@case('password')
								<div class="col-sm-5">
									<input type="text" class="form-control" id="{{ $field['name'] }}"
										   name="{{ $field['name'] }}"
										   autocomplete="new-password"
										   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
										   @if($mode == config('global.show') || isset($field['disabled'])) disabled @endif
									>
								</div>
								@if(isset($field['generate']))
									<div class="col-sm-3">
										<button type="button" name="get-password"
												id="get-password" class="btn btn-primary mb-3">
											Сгенерировать пароль
										</button>
									</div>
								@endif
								@break

								@case('textarea')
								<div class="col-sm-5">
								<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
										  cols="30"
										  rows="5"
										  @if($mode == config('global.show') || isset($field['disabled'])) disabled @endif
								>{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}</textarea>
								</div>
								@break

								@case('date')
								<div class="col-sm-5">
									<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
										   name="{{ $field['name'] }}" data-date-format="d.m.Y"
										   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
										   @if($mode == config('global.show') || isset($field['disabled'])) disabled @endif
									>
								</div>
								@break

								@case('select')
								<div class="col-sm-5">
									<select class="form-control select2" name="{{ $field['name'] }}"
											id="{{ $field['name'] }}"
											@if($mode == config('global.show') || isset($field['disabled'])) disabled @endif
									>
										@foreach($field['options'] as $key => $value)
											<option value="{{ $key }}"
													@if(isset($field['value']))
														@if($field['value'] == $key)
															selected
												@endif
												@endif
											>
												{{ $value }}</option>
										@endforeach
									</select>
								</div>
								@break

								@case('radio')
								<div class="col-sm-5">
									<div class="form-group btn-group col-lg-3 col-xs-12" role="group">
										@php
											$item = 1;
										@endphp
										@foreach($field['options'] as $key => $value)
											<input type="radio" class="btn-check" name="{{ $field['name'] }}"
												   id="{{ $field['name'] . $item }}"
												   autocomplete="off"
												   @if ($mode == config('global.create'))
													   @if($loop->first) checked @endif
												   @elseif ($key == $field['value'])
													   checked
												   @endif
												   @if ($mode == config('global.show'))
													   disabled
												   @endif
												   value="{{ $key }}"
											>
											<label class="btn btn-outline-primary"
												   for="{{ $field['name'] . $item++ }}">{{ $value }}</label>
										@endforeach
									</div>
								</div>
								@break

								@case('image')
								<div class="row items-push mb-4">
									<input type="file" class="form-control" id="{{ $field['name'] }}"
										   name="{{ $field['name'] }}"
										   onchange="readImage(this)"
										   @if($mode == config('global.show')) disabled @endif
									>
								</div>
								<div class="row mb-4 d-flex flex-column justify-content-start" id="panel_{{ $field['name'] }}">
									<div class="col-sm-9 mb-4">
										<img id="preview_{{ $field['name'] }}"
											@if ($mode == config('global.create'))
												src=""
												data-origin=""
											@else
												src="/uploads/{{ $field['value'] }}"
												data-origin="/uploads/{{ $field['value'] }}"
											@endif
											alt=""
											class="image-preview">
									</div>
									<div class="col-sm-3">
										<a class="btn btn-primary pl-3
										@if($mode == config('global.show')) disabled @endif clear-preview"
										   href="javascript:void(0)"
										   id="clear_{{ $field['name'] }}"
										   data-image="{{ $field['name'] }}"
										   role="button"
										>Сбросить изменения</a>
									</div>
								</div>
								@break

								@case('hidden')
								<input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}" value="{{ $field['value'] }}">
								@break

								@case('editor')
								<input type="hidden" id="{{ $field['name'] }}" name="{{ $field['name'] }}">
								<div class="col-sm-9">
									<div class="row">
										<div class="document-editor__toolbar"></div>
									</div>
									<div class="row row-editor">
										<div class="editor" id="{{ $field['name'] }}_editor"
											 name="{{ $field['name'] }}_editor">{!! $field['value'] ?? '' !!}</div>
									</div>
								</div>
								@break;
							@endswitch
							@switch($field['type'])
								@case('hidden')
								@break

								@default
						</div>
						@break
						@endswitch
						@endforeach
					</div>

					<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
						<div class="row">
							@yield('buttons')
						</div>
					</div>
	</form>
@endsection
