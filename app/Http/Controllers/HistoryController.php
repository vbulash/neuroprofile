<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Employer;
use App\Models\History;
use App\Models\Timetable;
use App\Models\User;
use App\Notifications\e2s\StartInternshipNotification;
use App\Support\PermissionUtils;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
	public function getData(Request $request)
	{
		$query = History::all();
		// TODO Отладить ограничения выборки через ids
		//if($request->has('ids'))
		// $query = $query->whereIn('id', $request->ids);

		return Datatables::of($query)
			->editColumn('employer', function ($history) {
				return $history->timetable->internship->employer->getTitle();
			})
			->editColumn('internship', function ($history) {
				return $history->timetable->internship->getTitle();
			})
			->editColumn('timetable', function ($history) {
				return $history->timetable->getTitle();
			})
			->editColumn('student', function ($history) {
				return $history->student->getTitle();
			})
			->addColumn('action', function ($history) {
				$editRoute = route('history.edit', ['history' => $history->id, 'sid' => session()->getId()]);
				$showRoute = route('history.show', ['history' => $history->id, 'sid' => session()->getId()]);
				$actions = '';

				if (!Auth::user()->hasRole('Практикант'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				if (Auth::user()->hasRole('Администратор')) {
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$history->id}, '')\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				}
				return $actions;
			})
			->make(true);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
	 */
    public function index()
    {
		$ids = null;
		$count = History::all()->count();
		if(Auth::user()->hasRole('Работодатель')) {
			// TODO Отладить ветку работодателя
		} elseif(Auth::user()->hasRole('Практикант')) {
			// TODO Отладить ветку практиканта
		}
		return view('histories.index', $ids ?
			compact('count', 'ids') :
			compact('count')
		);
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
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false)
	{
		$history = History::findOrFail($id);
		return view('histories.edit', compact('history', 'show'));
	}

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse
	 */
    public function update(Request $request, $id)
    {
		$history = History::findOrFail($id);
		$history->update($request->all());
		$history->notify(new StartInternshipNotification($history));

		session()->put('success', "Запись истории стажировки № " . $history->getKey() .
			" обновлена<br/>Письмо практиканту отправлено");
		return redirect()->route('history.index', ['sid' => session()->getId()]);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $history
	 * @return bool
	 */
	public function destroy(Request $request, int $history)
	{
		if ($history == 0) {
			$id = $request->id;
		} else $id = $history;

		$history = History::findOrFail($id);
		$history->delete();

		event(new ToastEvent('success', '', "Запись истории стажировок № {$id} удалена"));
		return true;
	}
}
