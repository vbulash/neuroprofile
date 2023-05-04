@extends('layouts.detail')

@section('buttons')
	<div class="col-sm-3 col-form-label">
		&nbsp;</div>
	<div class="col-sm-9">
		@if ($mode == config('global.show'))
			<a class="btn btn-primary pl-3" href="@yield('form.close')" role="button">Закрыть</a>
		@else
			<input type="hidden" name="then" id="then">
			<button type="submit" class="btn btn-primary mb-1" data-then="0">Сохранить</button>
			@if ($prev != 0)
				<button type="submit" class="btn btn-primary mb-1" data-then="{{ $prev }}">Сохранить и перейти на предыдущий
					блок</button>
			@endif
			@if ($next != 0)
				<button type="submit" class="btn btn-primary mb-1" data-then="{{ $next }}">Сохранить и перейти на следующий
					блок</button>
			@endif
			<a class="btn btn-secondary pl-3 mb-1" href="@yield('form.close')" role="button">Закрыть</a>
		@endif
	</div>
@endsection

@push('js_after')
	<script>
		document.getElementById("{{ $form }}").onsubmit = (event) => {
			document.getElementById('then').value = event.submitter.dataset.then
		}
	</script>
@endpush
