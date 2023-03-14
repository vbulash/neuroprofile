<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Client;
use App\Models\Contract;
use App\Models\License;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as ExcelException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\DataTables;
use Exception;

class ContractController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$context = session('context');
		$client = Client::findOrFail($context['client']);
		$contracts = $client->contracts();

		return Datatables::of($contracts)
			->editColumn('start', fn($contract) => $contract->start->format('d.m.Y'))
			->editColumn('end', fn($contract) => $contract->end->format('d.m.Y'))
			->addColumn('action', function ($contract) {
				$editRoute = route('contracts.edit', ['contract' => $contract->getKey()]);
				$showRoute = route('contracts.show', ['contract' => $contract->getKey()]);
				$selectRoute = route('contracts.select', ['contract' => $contract->getKey()]);

				$actions = "<a href=\"$editRoute\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-pencil-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"$showRoute\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete($contract->getKey(), '$contract->number')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"$selectRoute\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
					"<i class=\"fas fa-check\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	public function select(int $id): RedirectResponse {
		$context = session('context');
		unset($context['contract']);
		$context['contract'] = $id;
		session()->put('context', $context);

		return redirect()->route('contracts.info');
	}

	public function info(): Factory|View|Application {
		$context = session('context');
		$contract = Contract::findOrFail($context['contract']);

		$statuses = $contract->licenses->groupBy('status')->toArray();
		$statistics = [
			'Свободные лицензии' => (array_key_exists(License::FREE, $statuses) ? count($statuses[License::FREE]) : 0),
			'Используемые лицензии' => (array_key_exists(License::USING, $statuses) ? count($statuses[License::USING]) : 0),
			'Использованные лицензии' => (array_key_exists(License::USED, $statuses) ? count($statuses[License::USED]) : 0),
			'Поврежденные лицензии' => (array_key_exists(License::BROKEN, $statuses) ? count($statuses[License::BROKEN]) : 0),
		];

		return view('contracts.info', compact('contract', 'statistics'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function index(): View|Factory|RedirectResponse|Application {
		$context = session('context');
		unset($context['contract']);
		session()->put('context', $context);
		$client = Client::findOrFail($context['client']);

		$count = $client->contracts->count();
		return view('contracts.index', compact('count', 'client'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create(): View|Factory|Application {
		$mode = config('global.create');
		$context = session('context');
		$client = Client::findOrFail($context['client']);
		return view('contracts.create', compact('client', 'mode'));
	}

	public function store(StoreContractRequest $request) {
		$data = $request->all();
		$data['mkey'] = Contract::generateKey($request->url);
		$data['commercial'] = $request->has('commercial');
		$number = '';
		$count = 0;

		DB::transaction(function () use ($data, &$number, &$count) {
			$contract = Contract::create($data);
			$contract->save();
			$number = $contract->number;
			$count = $contract->license_count;

			// Сгенерировать $count свободных лицензий под текущий контракт
			event(new ToastEvent('info', '', "Генерация лицензий..."));
			$licenses = License::factory()->count($count)->make([
				'contract_id' => $contract->getKey()
			]);
			if ($licenses) {
				$licenses->each(function ($item, $key) {
					$item->save();
				}
				);
			}

			$contract->updateStatus();
		});

		session()->put('success', "Контракт № $number создан<br/>Сгенерированы лицензии: $count");
		return redirect()->route('contracts.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(int $id): View|Factory|Application {
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
		$contract = Contract::findOrFail($id);
		return view('contracts.edit', compact('contract', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateContractRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateContractRequest $request, $id): RedirectResponse {
		$contract = Contract::findOrFail($id);
		$count = 0;
		$current = $contract->license_count;

		$data = $request->all();
		$data['commercial'] = $request->has('commercial');

		DB::transaction(function () use ($data, $contract, $current, &$count) {
			$contract->update($data);
			$count = $data['license_count'] - $current;

			// Сгенерировать $count свободных лицензий под текущий контракт
			if ($count > 0) {
				event(new ToastEvent('info', '', "Генерация дополнительных лицензий..."));
				$licenses = License::factory()->count($count)->make([
					'contract_id' => $contract->getKey()
				]);
				if ($licenses) {
					$licenses->each(function ($item, $key) {
						$item->save();
					}
					);
				}
			}

			$contract->updateStatus();
		});
		$number = $contract->number;

		session()->put('success', "Контракт № $number обновлён " . ($count > 0 ? "<br/>Сгенерированы дополнительные лицензии: $count" : ""));
		return redirect()->route('contracts.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $contract
	 * @return bool
	 */
	public function destroy(Request $request, int $contract): bool {
		if ($contract == 0) {
			$id = $request->id;
		} else
			$id = $contract;

		$contract = Contract::findOrFail($id);
		$name = $contract->client->name;
		$number = $contract->number;
		$contract->delete();

		event(new ToastEvent('success', '', "Контракт № $number клиента '$name' удалён"));
		return true;
	}

	public function licensesExport(int $id) {
		event(new ToastEvent('info', '', "Формирование списка лицензий..."));

		$contract = Contract::find($id);
		$licenses = $contract->licenses->pluck('status', 'pkey')->toArray();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', sprintf("Лицензии клиента \"%s\" по контракту № %s",
			$contract->client->name, $contract->number));
		$sheet->setCellValue('A2', 'Персональный ключ');
		$sheet->setCellValue('B2', 'Статус лицензии');
		for ($row = 1; $row <= 2; $row++)
			for ($column = 1; $column <= 2; $column++) {
				$letter = Coordinate::stringFromColumnIndex($column);
				$style = $sheet->getStyle($letter . $row);
				$style->getFont()->setBold(true);
				$style->getFill()->setFillType(Fill::FILL_SOLID);
				$style->getFill()->getStartColor()->setRGB('B0B3B2');
			}
		$sheet->freezePane('A3');

		$row = 2;
		foreach ($licenses as $pkey => $status) {
			$sheet->setCellValue('A' . (++$row), $pkey);
			$statusText = '';
			switch ($status) {
				case License::FREE:
					$statusText = 'Свободная';
					break;
				case License::USING:
					$statusText = 'Используется';
					break;
				case License::USED:
					$statusText = 'Использована';
					break;
				case License::BROKEN:
					$statusText = 'Повреждена';
					break;
			}
			$sheet->setCellValue('B' . $row, $statusText);
		}
		event(new ToastEvent('success', '', "Список лицензий сформирован"));

		$tmpsheet = 'tmp/' . Str::uuid() . '.xlsx';
		$writer = new Xlsx($spreadsheet);
		try {
			Storage::makeDirectory('tmp');
			$writer->save(Storage::path($tmpsheet));
			return response()
				->download(Storage::path($tmpsheet), env('APP_NAME') . ' - Экспорт лицензий.xlsx')
				->deleteFileAfterSend();
		} catch (SpreadsheetException $e) {
		}
		return redirect()->back();
	}
}