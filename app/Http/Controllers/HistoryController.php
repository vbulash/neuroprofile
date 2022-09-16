<?php

namespace App\Http\Controllers;

use App\Models\FMPType;
use App\Models\History;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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
		$history = collect(DB::select(<<<EOS
SELECT
    history.id,
    history.done as timestamp,
    licenses.pkey as license,
    clients.name as client,
    contracts.number as contract,
    tests.name as test,
    history.card->"$.email" as email,
    contracts.commercial,
    history.paid
FROM history, licenses, tests, contracts, clients
WHERE
    licenses.id = history.license_id
    AND tests.id = history.test_id
    AND contracts.id = tests.contract_id
    AND clients.id = contracts.client_id
ORDER BY id DESC
EOS));
		$count = $history->count();

		return Datatables::of($history)
			->addColumn('commercial', fn($history) => $history->commercial ? 'Да' : 'Нет')
			->addColumn('paid', fn($history) => $history->paid ? 'Да' : 'Нет')
			->addColumn('action', function ($history) {
				$editRoute = route('history.edit', ['history' => $history->id, 'sid' => session()->getId()]);
				$showRoute = route('history.show', ['history' => $history->id, 'sid' => session()->getId()]);
				$mailRoute = route('payment.result', ['InvId' => $history->id, 'List' => true, 'sid' => session()->getId()]);

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
				if ($history->test->contract->commercial)
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$history->id})\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				$actions .=
					"<a href=\"$mailRoute\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
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
	public function index()
	{
		$count = History::all()->count();
		return view('history.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}
