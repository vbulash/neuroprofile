@extends('layouts.backend')

@section('content')
	<i style="color: red">Здесь будет главная страница платформы</i>
@endsection

@push('scripts.injection')
	<script>
		console.log('session at dashboard = {!! session()->getId() !!}');
	</script>
@endpush

