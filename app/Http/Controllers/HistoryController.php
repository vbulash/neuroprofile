<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\results\CardComposer;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\FMPType;
use App\Models\History;
use App\Models\License;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

class HistoryController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse
	{
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
EOS);
		$count = count($histories);

		return Datatables::of($histories)
			->editColumn('timestamp', fn($history) => (new DateTime($history->timestamp))->format('d.m.Y G:i:s'))
			->editColumn('commercial', fn($history) => $history->commercial ? 'Да' : 'Нет')
			->editColumn('paid', fn($history) => $history->paid ? 'Да' : 'Нет')
			->editColumn('action', function ($history) {
				$editRoute = route('history.edit', ['history' => $history->id, 'sid' => session()->getId()]);
				$showRoute = route('history.show', ['history' => $history->id, 'sid' => session()->getId()]);

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
	public function index(): View|Factory|Application
	{
		$count = History::all()->count();
		event(new ToastEvent('error', '',
			"Проверка toast-системы"));
		return view('history.index', compact('count'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(int $id): Application|Factory|View
	{
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false): View|Factory|Application
	{
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
	public function update(UpdateHistoryRequest $request, int $id): RedirectResponse
	{
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
		if( count($updates) > 0) {
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
	public function destroy(Request $request, int $history): bool
	{
		if ($history == 0) {
			$id = $request->id;
		} else $id = $history;

		$h = History::findOrFail($id);
		$h->license->status = License::FREE;
		$h->license->update();
		$h->delete();

		/** @var int $id */
		event(new ToastEvent('success', '',
			"Запись истории № {$id} удалена<br/>Лицензию можно использовать повторно"));
		return true;
	}
}
