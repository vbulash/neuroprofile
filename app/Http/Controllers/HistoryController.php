<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\results\CardComposer;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\History;
use App\Models\License;
use ArrayObject;
use DateTime;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Yajra\DataTables\DataTables;

class HistoryController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse {
		$histories = DB::select(<<<EOS
SELECT
    history.id,
    history.done as timestamp,
    licenses.pkey as license,
    clients.name as client,
    contracts.number as contract,
    tests.name as test,
    history.card->>"$.email" as email,
    contracts.commercial,
    history.paid
FROM history, licenses, tests, contracts, clients
WHERE
    licenses.id = history.license_id
    AND tests.id = history.test_id
    AND contracts.id = tests.contract_id
    AND clients.id = contracts.client_id
ORDER BY id DESC
EOS
		);
		$count = count($histories);

		return Datatables::of($histories)
			->editColumn('date', fn($history) => (new DateTime($history->timestamp))->format('d.m.Y'))
			->editColumn('time', fn($history) => (new DateTime($history->timestamp))->format('H:i:s'))
			->editColumn('commercial', fn($history) => $history->commercial ? 'Да' : 'Нет')
			->editColumn('paid', fn($history) => $history->paid ? 'Да' : 'Нет')
			->editColumn('action', function ($history) {
			    $editRoute = route('history.edit', ['history' => $history->id]);
			    $showRoute = route('history.show', ['history' => $history->id]);

			    $actions =
			    	"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
			    	"<i class=\"fas fa-pencil-alt\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
			    	"<i class=\"fas fa-eye\"></i>\n" .
			    	"</a>\n";
			    if ($history->commercial)
				    $actions .=
				    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left\" " .
				    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$history->id})\">\n" .
				    	"<i class=\"fas fa-trash-alt\"></i>\n" .
				    	"</a>\n";
			    $actions .=
			    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Повтор письма\" onclick=\"clickMail({$history->id})\">\n" .
			    	"<i class=\"fas fa-envelope\"></i>\n" .
			    	"</a>\n";

			    return $actions;
		    })
				//;
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index(): View|Factory|Application {
		$count = History::all()->count();

		$row = 0;
		$fields = [];
		foreach (History::$groups as $group) {
			$children = [];
			foreach ($group['fields'] as $field) {
				$id = $row++;
				if (isset($field['hidden']))
					continue;
				$children[] = ['id' => $id, 'text' => $field['title']];
			}
			$fields[] = [
				'text' => $group['label'],
				'children' => $children
			];
		}

		$fields = json_encode($fields);
		return view('history.index', compact('count', 'fields'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(int $id): Application|Factory|View {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false): View|Factory|Application {
		$mode = $show ? config('global.show') : config('global.edit');
		$history = History::findOrFail($id);
		$composer = new CardComposer($history);
		$card = $composer->getCard(true);
		return view('history.edit', compact('history', 'mode', 'card'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateHistoryRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateHistoryRequest $request, int $id): RedirectResponse {
		$history = History::findOrFail($id);
		$updates = [];
		$card = json_decode($history->card);
		if (isset($card->email) && $card->email != $request->email) {
			$card->email = $request->email;
			$updates['card'] = json_encode($card);
		}
		if ($history->paid != $request->has('paid')) {
			$updates['paid'] = $request->has('paid');
		}
		if (count($updates) > 0) {
			$history->update($updates);
			session()->put('success', "Запись истории тестирования № {$history->getKey()} обновлена");
		}

		return redirect()->route('history.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $history
	 * @return bool
	 */
	public function destroy(Request $request, int $history): bool {
		if ($history == 0) {
			$id = $request->id;
		} else
			$id = $history;

		$h = History::findOrFail($id);
		$h->license->status = License::FREE;
		$h->license->update();
		$h->delete();

		/** @var int $id */
		event(new ToastEvent('success', '',
			"Запись истории № {$id} удалена<br/>Лицензию можно использовать повторно"));
		return true;
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function export(Request $request): RedirectResponse {
		event(new ToastEvent('info', '', "Формирование экспортных данных истории тестирования..."));

		$from = $request->from ? new DateTime($request->from) : new DateTime('1969-07-01');
		$to = $request->to ? (new DateTime($request->to))->modify('+1 day -1 microsecond') : new DateTime('2200-01-01');

		$sql = [];
		$fieldList = json_decode($request->get('field-list'));
		$fields = History::getFields();
		if (count($fieldList) == 0) {
			foreach ($fields as $number => $field) {
				$fieldList[] = $number;
				$sql[] = $field['sql'];
			}
		} else {
			$temp = (new ArrayObject($fieldList))->getArrayCopy();
			$fieldList = [];
			foreach ($fields as $number => $field) {
				if (isset($field['hidden']) || in_array($number, $temp)) {
					$fieldList[] = $number;
					if (!isset($field['special']))
						$sql[] = $field['sql'];
				}
			}
		}
		$sql = implode(', ', $sql);
		$sql = sprintf(<<<EOS
SELECT DISTINCT %s
FROM history, tests, sets, historysteps, questions, contracts, clients, licenses
WHERE
	tests.id=history.test_id AND
	sets.id=tests.set_id AND
	historysteps.history_id=history.id AND
	questions.id=historysteps.question_id AND
	contracts.id=tests.contract_id AND
	clients.id=contracts.client_id AND
	licenses.id=history.license_id AND
	history.done BETWEEN :from AND :to
ORDER BY
	history.id, questions.sort_no
EOS, $sql);

		$histories = DB::select($sql,
			['from' => $from->format('Y-m-d G:i:s.u'), 'to' => $to->format('Y-m-d G:i:s.u')]
		);

		if (count($histories) == 0) {
			session()->put('error', "Заданы слишком жесткие условия фильтра - записи истории не найдены<br/>Исправьте ваш фильтр экспорта");
			return redirect()->back();
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', sprintf("Подробная история прохождения тестирования за период %s%s",
			$request->from ? 'с ' . $from->format('d.m.Y') . ' ' : ' ',
			$request->to ? 'по ' . $to->format('d.m.Y') : ''));

		$column = 1;
		$fields = History::getFields();
		foreach ($fieldList as $number) {
			if (isset($fields[$number]['special']))
				continue;
			$name = $fields[$number]['title'];
			$letter = Coordinate::stringFromColumnIndex($column++);
			$sheet->setCellValue($letter . '2', $name);
		}
		$sheet->freezePane('A3');

		$row = 2;
		foreach ($histories as $history) {
			$row++;
			$column = 1;
			foreach ($fieldList as $number) {
				$letter = Coordinate::stringFromColumnIndex($column++);
				if (isset($fields[$number]['special'])) {
					$special = $fields[$number]['special'];
					if ($special == 'answers') {
						$sheet->setCellValue($letter . '2', 'Блок ответов на вопросы');
						$hist = History::findOrFail($history->id);
						foreach ($hist->steps as $step) {
							$sheet->setCellValue($letter . $row, $step->key);
							$letter = Coordinate::stringFromColumnIndex($column++);
						}
					}
				} else
					try {
						$result = eval($fields[$number]['code']);
						if ($result == null || $result == 'null')
							$result = '';
					} catch (Exception $exc) {
						$result = '';
					} finally {
						$sheet->setCellValue($letter . $row, $result);
					}
			}
		}

		//		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel; charset=utf-8');
		header('Content-Disposition: attachment;filename="' . env('APP_NAME') . ' - Экспорт истории тестирования.xlsx' . '"');
		header('Cache-Control: max-age=0');
		//		ob_end_clean();

		event(new ToastEvent('success', '', "Данные для экспорта истории тестирования сформированы"));

		$writer = new Xlsx($spreadsheet);
		try {
			$writer->save('php://output');
		} catch (SpreadsheetException $e) {
		}

		return redirect()->back();
	}
}
