<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreFMPTypeRequest;
use App\Http\Requests\UpdateFMPTypeRequest;
use App\Models\Client;
use App\Models\FMPType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Store;
use Yajra\DataTables\DataTables;
use Exception;

class FMPTypeController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$fmptypes = FMPType::all();

		return Datatables::of($fmptypes)
			->editColumn('cluster', fn ($fmptype) => $fmptype->cluster ? 'Нейропрофиль' : 'ФМП')
			->editColumn('active', fn ($fmptype) => $fmptype->active ? 'Активный' : 'Неактивный')
			->addColumn('fact', fn ($fmtype) => 0)	// TODO сделать после добавления профилей
			->addColumn('action', function ($fmptype) {
				$editRoute = route('fmptypes.edit', ['fmptype' => $fmptype->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('fmptypes.show', ['fmptype' => $fmptype->getKey(), 'sid' => session()->getId()]);
				$selectRoute = route('fmptypes.select', ['fmptype' => $fmptype->getKey(), 'sid' => session()->getId()]);
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
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$fmptype->getKey()}, '{$fmptype->name}')\">\n" .
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
		$fmptype = FMPType::findOrFail($id);
		session()->forget('context');
		session()->put('context', ['fmptype' => $fmptype]);

		//return redirect()->route('profiles.index', ['sid' => session()->getId()]);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
	 */
    public function index()
    {
        session()->forget('context');

		$count = FMPType::all()->count();
		return view('fmptypes.index', compact('count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
	 */
    public function create()
    {
        $mode = config('global.create');
		return view('fmptypes.create', compact('mode'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFMPTypeRequest $request
     * @return RedirectResponse
	 */
    public function store(StoreFMPTypeRequest $request)
    {
        $data = $request->except('_token');
		$data['active'] = false;
		$fmptype = FMPType::create($data);
		$fmptype->save();

		$name = $fmptype->name;

		session()->put('success', "Тип описания \"{$name}\" создан");
		return redirect()->route('fmptypes.index', ['sid' => session()->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
	 */
    public function show($id)
    {
        return $this->edit($id, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
	 */
    public function edit($id, bool $show = false)
    {
		$mode = $show ? config('global.show') : config('global.edit');
		$fmptype = FMPType::findOrFail($id);
		return view('fmptypes.edit', compact('fmptype', 'mode'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateFMPTypeRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
    public function update(UpdateFMPTypeRequest $request, $id)
    {
		$fmptype = FMPType::findOrFail($id);
		$name = $fmptype->name;
		$fmptype->update($request->except('_token'));

		session()->put('success', "Тип описания \"{$name}\" обновлён");
		return redirect()->route('fmptypes.index', ['sid' => session()->getId()]);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $fmptype
	 * @return bool
	 */
	public function destroy(Request $request, int $fmptype)
    {
		if ($fmptype == 0) {
			$id = $request->id;
		} else $id = $fmptype;

		$fmptype = FMPType::findOrFail($id);
		$name = $fmptype->name;
		$fmptype->delete();

		event(new ToastEvent('success', '', "Тип описания '{$name}' удалён"));
		return true;
    }
}
