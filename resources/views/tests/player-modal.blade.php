<div class="modal fade" id="tests-play" tabindex="-1" aria-labelledby="tests-play-label" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tests-create-label">Проверочный плеер тестов</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Выберите тест для проверки прохождения:</p>
                <div class="d-flex align-items-start">
                    <div class="form-control mb-4" id="no-tests" style="display: none;">
                        <p>Нет доступных тестов для проверки</p>
                    </div>
                    <select name="tests" class="select2 form-control" style="display:none;width:100%;" id="tests" size="10">
                    </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
				{{-- TODO Вставить запуск теста --}}
                <form role="form" method="get" action="#"
                      id="start-player">
                    @csrf
                    <input type="hidden" name="mkey-modal" id="mkey-modal">
                    <input type="hidden" name="test" id="test">
                    <button type="submit" id="play_btn" class="btn btn-primary" data-role="submit">Проверить</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js_after')
    <script>
        if (!String.format) {
            String.format = function(format) {
                var args = Array.prototype.slice.call(arguments, 1);
                return format.replace(/{(\d+)}/g, function(match, number) {
                    return typeof args[number] != 'undefined'
                        ? args[number]
                        : match
                        ;
                });
            };
        }

        $(function () {
            $.get({
                url: "{{ route('tests.list', ['sid' => $sid]) }}",
                method: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: (data) => {
                    let tests = JSON.parse(data);
                    if (tests.length === 0) {
                        $('#tests').show();
                        $('#tests').hide();
                    } else {
                        $('#no-tests').hide();
                        let first = true;
                        let html = '';
                        $.each(tests, function (key, value) {
                            html = html + String.format('<option {0} data-mkey="{1}" data-test="{2}" value="{3}">{4}</option>',
                                (first ? "selected " : ""), value.mkey, value.test, value.id, value.name);
                            if (first) first = false;
                        });
                        $('#tests').html(html);
                        $('#tests').show();
                    }
                }
            });

            $('#play_btn').on('click', () => {
                let mkey = $('#tests').find(':selected').data('mkey');
                let test = $('#tests').find(':selected').data('test');
                $('#mkey-modal').val(mkey);
                $('#test').val(test);
            });
        });
    </script>
@endpush
