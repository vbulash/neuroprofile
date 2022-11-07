@php
	use App\Models\TestOptions;
	$mousetracking = intval($test->options) & TestOptions::MOUSE_TRACKING->value;
@endphp

@extends('front.layouts.layout')

@push('title')
	- Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('testname')
	Тест &laquo;{{ $test->name }}&raquo;
@endpush

@push('step_description')
	Осталось секунд:
@endpush

@section('content')
	<form method="post" action="{{ route('player.body2.store') }}" enctype="multipart/form-data" name="play-form"
		id="play-form">
		@csrf
		<input type="hidden" name="sid" value="{{ $sid }}">

		<!-- Preloader -->
		{{--        <div class="preloader"> --}}
		{{--            <h4 class="mt-4 text-center">Загрузка вопросов теста...</h4> --}}
		{{--        </div> --}}

		<div>
			@foreach ($questions as $question)
				@php
					if ($question->kind->images <= 2) {
					    $columns = 2;
					    $grid = 6;
					} else {
					    $columns = 3;
					    $grid = 4;
					}
					$rows = intval(ceil($question->kind->images / $columns));
				@endphp

				<input type="hidden" name="answer-{{ $question->getKey() }}" id="answer-{{ $question->getKey() }}">
				<div id="div-{{ $question->getKey() }}" class="question-div" style="display: none">
					<h4 class="mt-4 mb-4 text-center">
						@if (isset($question->cue))
							{!! $question->cue !!}
						@elseif (isset($question->kind->cue))
							{!! $question->kind->cue !!}
						@endif
					</h4>

					@php($imageNo = 0)
					<div class="d-flex flex-wrap test-row">
						@foreach ($question->parts as $part)
							<div class="col-{{ $grid }}">
								<div class="me-4 mb-4">
									<img src="/uploads/{{ $part->image }}" data-id="{{ $question->getKey() }}"
										data-sort-no="{{ $question->sort_no }}" data-key="{{ $part->key }}" alt=""
										class="step-image img-fluid">
								</div>
							</div>
							@php($imageNo++)
						@endforeach
					</div>
				</div>
			@endforeach
		</div>
	</form>
@endsection

@push('scripts.injection')
	<script>
		// Очередь вопросов
		function Questions() {
			this.stack = null;
			this.pointer = -1;

			this.init = function() {
				if (this.stack) return;

				this.stack = new Map();
				let index = 0;

				@foreach ($questions as $question)
					this.stack.set({{ $question->getKey() }}, {
						id: {{ $question->getKey() }},
						sort_no: {{ $question->sort_no }},
						learning: {{ $question->learning }},
						timeout: {{ $question->timeout }},
						answers: {{ $question->kind->answers }},
					});
				@endforeach
			}

			this.next = function() {
				if (!this.stack) this.init();

				this.pointer++;
				return this.pointer < this.stack.size;
			}

			this.get = function() {
				if (!this.stack) this.init();

				if (this.pointer === -1) this.pointer = 0;
				if (this.pointer >= this.stack.size) return null;

				let key = Array.from(this.stack.keys())[this.pointer];
				return this.stack.get(key);
			}

			this.again = function() {
				if (!this.stack) this.init();

				let element = this.get();
				this.stack.delete(element.id);
				this.stack.set(element.id, element);
			}

			this.map = function() {
				return this.stack;
			}
		}

		let questions = new Questions();
		// let first = true;
		// while (questions.next()) {
		//     let element = questions.get();
		//     if (first && (element.id === "205")) {
		//         first = false;
		//         questions.again();
		//     }
		// }

		// Подготовка отображения вопроса
		function prepareQuestion() {
			let div = null;
			window.pressed = false;
			let element = questions.get();
			if (window.slide !== undefined) { // Сначала нужно погасить предыдущие слайды
				div = document.getElementById('div-' + window.slide);
				div.style.display = 'none';
			}
			window.slide = element.id;

			@if ($mousetracking)
				document.dispatchEvent(new Event('mousemove'));
			@endif

			div = document.getElementById('div-' + window.slide);
			div.style.display = 'block';
			startTimers();
		}

		function startTimers() {
			let element = questions.get();
			let counter = document.getElementById('step-countdown');
			if (element.timeout === '0') {
				document.querySelectorAll('.step-countdown').forEach((counter) => {
					counter.innerText = 'таймаут выключен';
				});
				return;
			}

			let form = document.getElementById('play-form');
			form.addEventListener('submit', event => {
				if (window.submitted) {
					@if ($mousetracking)
						document.removeEventListener('mousemove', mouseListener, false);
					@endif
					event.stopPropagation();
					event.stopImmediatePropagation();
				} else window.submitted = true;
			});

			document.querySelectorAll('.step-countdown').forEach((counter) => {
				counter.innerText = element.timeout;
			});
			window.counter = parseInt(element.timeout);

			window.timer = setInterval(() => {
				//console.log(window.counter);
				document.querySelectorAll('.step-countdown').forEach((counter) => {
					counter.innerText = window.counter;
				});
				window.counter--;
				if (window.counter < 0) {
					clearInterval(window.timer);
					document.getElementById('answer-' + element.id).value = 0;

					if (element.learning === 0) {
						questions.again();
						prepareQuestion();
						//console.log('real again:');
						//console.log([...questions.stack.keys()]);
					} else if (questions.next()) {
						prepareQuestion();
						//console.log('learn skip:');
						//console.log([...questions.stack.keys()]);
					} else {
						document.getElementById('play-form').submit();
					}
				}
			}, 1000);
		}

		function stopTimers() {
			if (window.timer) clearInterval(window.timer);
		}

		// Нажатие на картинку вопроса
		document.querySelectorAll(".step-image").forEach((pic) => {
			pic.addEventListener('click', event => {
				let question = questions.get();
				if (question.answers == 1) {
					// Предотвращение повторных нажатий
					if (window.pressed) {
						event.stopPropagation();
						event.stopImmediatePropagation();
						return;
					}
					window.pressed = true;

					// Зафиксировать результат нажатия
					let image = event.target;

					let qid = image.dataset.id;
					document.getElementById('answer-' + qid).value = image.dataset.key;

					stopTimers();

					// Переключиться на следующий вопрос
					if (questions.next()) {
						prepareQuestion();
					} else {
						document.getElementById('play-form').submit();
					}
				} else {
					// TODO Отработать множественные ответы
				}
			}, false);
		});

		@if ($mousetracking)
			// Трекинг мыши

			let mousePos;
			let mouseMoves = [];
			let mouseListener = function(event) {
				let dot, eventDoc, doc, body, pageX, pageY;

				event = event || window.event; // IE-ism

				// If pageX/Y aren't available and clientX/Y are,
				// calculate pageX/Y - logic taken from jQuery.
				// (This is to support old IE)
				if (event.pageX == null && event.clientX != null) {
					eventDoc = (event.target && event.target.ownerDocument) || document;
					doc = eventDoc.documentElement;
					body = eventDoc.body;

					event.pageX = event.clientX +
						(doc && doc.scrollLeft || body && body.scrollLeft || 0) -
						(doc && doc.clientLeft || body && body.clientLeft || 0);
					event.pageY = event.clientY +
						(doc && doc.scrollTop || body && body.scrollTop || 0) -
						(doc && doc.clientTop || body && body.clientTop || 0);
				}

				let X = Math.min(event.pageX / window.innerWidth, 1);
				let Y = Math.min(event.pageY / window.innerHeight, 1);
				console.log((new Date()).getTime().toString() + ' : ID вопроса = ' + window.slide.toString() + ' : X = ' +
					X.toString() + ' / Y = ' + Y.toString());
			}

			// TODO вернуть mouse-tracker
			//document.addEventListener('mousemove', mouseListener, false);
		@endif

		document.addEventListener("DOMContentLoaded", () => {
			prepareQuestion();
			window.submitted = false;
		}, false);
	</script>
@endpush
