<div class="block-content ps-5 pe-5">
	<div class="row mb-4">
		<label class=" col-sm-3 col-form-label" for="link">@if($show) Данная анкета связана с записью пользователя @else Свяжите данную анкету работодателя с записью
			пользователя @endif
			@hasrole('Практикант') (только текущий пользователь) @endhasrole
			@hasrole('Работодатель') (только текущий пользователь) @endhasrole
			@if(!$show) <span class="required">*</span> @endif
		</label>
		<div class="col-sm-5 col-form-label">
			<select name="link" id="link" class="form-control select2" @if($show) disabled @endif>
				@hasrole('Администратор')
				<option selected disabled>Выберите пользователя</option>
				@foreach($users as $key => $value)
					<option value="{{ $key }}"
							@if(isset($employer) && $employer->user_id = $value)
								selected
							@elseif(\Illuminate\Support\Facades\Auth::user()->name == $value)
								selected
							@endif
					>
						{{ $value }}</option>
				@endforeach
				@endhasrole

				@hasrole('Работодатель')
				<option value="{{ \Illuminate\Support\Facades\Auth::user()->id }}">
					{{ \Illuminate\Support\Facades\Auth::user()->name }}
				</option>
				@endhasrole

				@hasrole('Практикант')
				<option value="{{ \Illuminate\Support\Facades\Auth::user()->id }}">
					{{ \Illuminate\Support\Facades\Auth::user()->name }}
				</option>
				@endhasrole
			</select>
		</div>
	</div>
</div>
