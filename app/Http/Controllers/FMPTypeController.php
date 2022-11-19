<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreFMPTypeRequest;
use App\Http\Requests\UpdateFMPTypeRequest;
use App\Models\FMPType;
use App\Models\Profile;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
			->addColumn('fact', fn ($fmptype) => $fmptype->profiles->count())
			->addColumn('action', function ($fmptype) {
				$editRoute = route('fmptypes.edit', ['fmptype' => $fmptype->getKey()]);
				$showRoute = route('fmptypes.show', ['fmptype' => $fmptype->getKey()]);
				$selectRoute = route('fmptypes.select', ['fmptype' => $fmptype->getKey()]);
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
		session()->forget('context');
		session()->put('context', ['fmptype' => $id]);

		return redirect()->route('profiles.index');
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
		$max = count(Profile::getAllCodes());
		return view('fmptypes.create', compact('mode', 'max'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFMPTypeRequest $request
     * @return RedirectResponse
	 */
    public function store(StoreFMPTypeRequest $request)
    {
        $data = $request->except(['_token', 'ethalon']);
		$data['active'] = false;
		$data['ethalon'] = $request->has('ethalon');
		$fmptype = FMPType::create($data);
		$fmptype->save();

		$name = $fmptype->name;

		session()->put('success', "Тип описания \"{$name}\" создан");
		return redirect()->route('fmptypes.index');
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
		$max = count(Profile::getAllCodes());
		return view('fmptypes.edit', compact('fmptype', 'mode', 'max'));
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
		$data = $request->except(['_token', 'id', 'ethalon']);	// ID нужен только для валидации
		$count = $fmptype->profiles->count();
		$data['active'] = intval($data['limit']) == $count;
		$data['ethalon'] = $request->has('ethalon');
		$name = $fmptype->name;
		$fmptype->update($data);

		session()->put('success', "Тип описания \"{$name}\" обновлён");
		return redirect()->route('fmptypes.index');
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
