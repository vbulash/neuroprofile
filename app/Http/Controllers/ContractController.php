<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\License;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

class ContractController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$context = session('context');
		$contracts = $context['client']->contracts();

		return Datatables::of($contracts)
			->editColumn('start', fn($contract) => $contract->start->format('d.m.Y'))
			->editColumn('end', fn($contract) => $contract->end->format('d.m.Y'))
			->addColumn('action', function ($contract) {
				$editRoute = route('contracts.edit', ['contract' => $contract->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('contracts.show', ['contract' => $contract->getKey(), 'sid' => session()->getId()]);
				$selectRoute = route('contracts.select', ['contract' => $contract->getKey(), 'sid' => session()->getId()]);
				$actions = '';

				$actions .=
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-pencil-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$contract->getKey()}, '{$contract->number}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
					"<i class=\"fas fa-check\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	public function select(int $id)
	{
		$context = session('context');
		unset($context['contract']);
		$context['contract'] = Contract::findOrFail($id);
		session()->put('context', $context);

		return redirect()->route('contracts.info', ['sid' => session()->getId()]);
	}

	public function info() {
		$context = session('context');
		$contract = $context['contract'];

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
	public function index()
	{
		$context = session('context');
		unset($context['contract']);
		session()->put('context', $context);

		$count = $context['client']->contracts()->count();
		return view('contracts.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create()
	{
		$context = session('context');
		$client = $context['client'];
		return view('contracts.create', compact('client'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreContractRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreContractRequest $request)
	{
		$data = $request->all();
		$data['mkey'] = Contract::generateKey($request->url);
		$number = '';
		$count = 0;

		DB::transaction(function () use ($request, $data, &$number, &$count) {
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
				});
			}

			$contract->updateStatus();
		});

		session()->put('success', "Контракт № {$number} создан<br/>Сгенерированы лицензии: {$count}");
		return redirect()->route('contracts.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id)
	{
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false)
	{
		$contract = Contract::findOrFail($id);
		return view('contracts.edit', compact('contract', 'show'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateContractRequest $request, $id)
	{
		$contract = Contract::findOrFail($id);
		$count = 0;
		$current = $contract->license_count;
		DB::transaction(function () use ($request, $contract, $current, &$count) {
			$contract->update($request->all());
			$count = $request->license_count - $current;

			// Сгенерировать $count свободных лицензий под текущий контракт
			if ($count > 0) {
				event(new ToastEvent('info', '', "Генерация дополнительных лицензий..."));
				$licenses = License::factory()->count($count)->make([
					'contract_id' => $contract->getKey()
				]);
				if ($licenses) {
					$licenses->each(function ($item, $key) {
						$item->save();
					});
				}
			}

			$contract->updateStatus();
		});
		$number = $contract->number;

		session()->put('success', "Контракт № {$number} обновлён " . ($count > 0 ? "<br/>Сгенерированы дополнительные лицензии: {$count}" : ""));
		return redirect()->route('contracts.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $contract
	 * @return bool
	 */
	public function destroy(Request $request, int $contract)
	{
		if ($contract == 0) {
			$id = $request->id;
		} else $id = $contract;

		$contract = Contract::findOrFail($id);
		$name = $contract->client->name;
		$number = $contract->number;
		$contract->delete();

		event(new ToastEvent('success', '', "Контракт № {$number} клиента '{$name}' удалён"));
		return true;
	}
}
