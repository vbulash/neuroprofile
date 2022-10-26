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
	<form method="post" action="{{ route('player.body2.store') }}" enctype="multipart/form-data" {{--          onsubmit="if(submitted) return false; submitted = true; return true" --}}
		name="play-form" id="play-form">
		@csrf
		<input type="hidden" name="sid" value="{{ $sid }}">

		<!-- Preloader -->
		{{--        <div class="preloader"> --}}
		{{--            <h4 class="mt-4 text-center">Загрузка вопросов теста...</h4> --}}
		{{--        </div> --}}


		@php
			$rows = 1;
			$columns = 2;
			//				if(count($step['images']) == \App\Models\TestOptions::IMAGES2->value) {
			//					$rows = 1;
			//					$columns = 2;
			//				} elseif(count($step['images']) == \App\Models\TestOptions::IMAGES4->value) {
			//					$rows = 2;
			//					$columns = 2;
			//				}
			$grid = intval(12 / $columns);
		@endphp

		<div>
			@foreach ($steps as $step)
				@php
					$keys = array_keys($step['images']);
				@endphp

				<input type="hidden" name="answer-{{ $step['id'] }}" id="answer-{{ $step['id'] }}">
				<div id="div-{{ $step['id'] }}" class="question-div" style="display: none">
					<h4 class="mt-4 mb-4 text-center">
						@if (isset($step['cue']))
							{{ $step['cue']}}
						@else
							Выберите одно изображение
						@endif
					</h4>
					@for ($row = 0; $row < $rows; $row++)
						@php($imageNo = 0)
						<div class="row test-row mb-4">
							@for ($column = 0; $column < $columns; $column++)
								<div class="col-{{ $grid }}">
									<div class="@if ($column == 0) mr-4 @endif">
										<img src="/uploads/{{ $keys[$imageNo] }}" data-id="{{ $step['id'] }}"
											data-sort-no="{{ $step['sort_no'] }}" data-idx="{{ $imageNo + 1 }}"
											data-key="{{ $step['images'][$keys[$imageNo]] }}" alt=""
											class="step-image img-fluid">
									</div>
								</div>
								@php($imageNo++)
							@endfor
						</div>
					@endfor
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

				let json = {!! json_encode($cuts) !!};
				while (index in json) {
					let question = json[index++];
					let element = {
						id: question.id,
						sort_no: question.sort_no,
						learning: question.learning,
						timeout: question.timeout
					};
					this.stack.set(element.id, element);
				}
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
			//console.log('window.pressed = ' + window.pressed.toString());
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
						// TODO Комментируем только на время отладки устранения дублей
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
				// Предотвращение повторных нажатий
				if (window.pressed) {
					event.stopPropagation();
					event.stopImmediatePropagation();
					return;
				}
				//debugger;
				window.pressed = true;

				// Зафиксировать результат нажатия
				let image = event.target;

				let qid = image.dataset.id;
				// TODO постепенно перейти с использования ключа на использование номера картинки
				document.getElementById('answer-' + qid).value = image.dataset.key;
				//document.getElementById('answer-' + qid).value = image.dataset.idx;

				stopTimers();

				// Переключиться на следующий вопрос
				if (questions.next()) {
					prepareQuestion();
					//console.log('next: ');
					//console.log([...questions.stack.keys()]);
				} else {
					document.getElementById('play-form').submit();
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
			console.log('session at player = {!! session()->getId() !!}');
			prepareQuestion();
			window.submitted = false;
		}, false);
	</script>
@endpush
