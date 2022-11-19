<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

class ClientController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$clients = Client::all();

		return Datatables::of($clients)
			->addColumn('contracts', function($client) {
				$count = $client->contracts->count();
				switch ($count) {
					case 0:
					case null:
						return 'Нет';
					default:
						return $count;
				}
			})
			->addColumn('action', function ($client) {
				$editRoute = route('clients.edit', ['client' => $client->getKey()]);
				$showRoute = route('clients.show', ['client' => $client->getKey()]);
				$selectRoute = route('clients.select', ['client' => $client->getKey()]);
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
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$client->getKey()}, '{$client->name}')\">\n" .
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
		session()->forget('context');
		session()->put('context', ['client' => $id]);

		return redirect()->route('contracts.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function index()
	{
		session()->forget('context');
		$count = Client::all()->count();
		return view('clients.index', compact('count'));
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
	 */
    public function create()
    {
		$mode = config('global.create');
        return view('clients.create', compact('mode'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreClientRequest $request
	 * @return RedirectResponse
	 */
    public function store(StoreClientRequest $request)
    {
		$client = Client::create($request->all());
		$client->save();
		$name = $client->name;

		session()->put('success', "Клиент \"{$name}\" создан");
		return redirect()->route('clients.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
	 */
    public function show(int $id)
    {
        return $this->edit($id, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
	 */
    public function edit(int $id, bool $show = false)
    {
		$mode = $show ? config('global.show') : config('global.edit');
		$client = Client::findOrFail($id);
        return view('clients.edit', compact('client', 'mode'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateClientRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
    public function update(UpdateClientRequest $request, $id)
    {
		$client = Client::findOrFail($id);
		$name = $client->name;
		$client->update($request->all());

		session()->put('success', "Клиент \"{$name}\" обновлён");
		return redirect()->route('clients.index');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $client
	 * @return bool
	 */
	public function destroy(Request $request, int $client)
	{
		if ($client == 0) {
			$id = $request->id;
		} else $id = $client;

		$client = Client::findOrFail($id);
		$name = $client->name;
		$client->delete();

		event(new ToastEvent('success', '', "Клиент '{$name}' удалён"));
		return true;
	}
}
