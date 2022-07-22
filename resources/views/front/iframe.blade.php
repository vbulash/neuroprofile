@extends('front.layouts.layout')

@push('title') - Тестирование IFrame @endpush

@section('content')
    <h1 class="mb-5">Проверка IFrame</h1>
    <!-- IFrame -->
    <iframe
        src="http://psycho.bulash.ru/player.play/mkey_60501e2ae282a2.08095650*2427387986/test_60cf61faa26899.54714870"
        width="1000px"
        height="700px"
        allow="fullscreen">
    </iframe>
    <!-- /.IFrame -->
@endsection

@push('scripts.injection')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            //
        }, false);
    </script>
@endpush
