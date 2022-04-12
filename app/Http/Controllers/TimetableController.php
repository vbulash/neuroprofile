<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreTimetableRequest;
use App\Http\Requests\UpdateTimetableRequest;
use App\Models\Employer;
use App\Models\Internship;
use App\Models\Timetable;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use \Exception;

class TimetableController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @param int $internship
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request, int $internship)
	{
		$query = Internship::findOrFail($internship)->timetables()->get();

		return Datatables::of($query)
			->editColumn('start', function ($timetable) {
				switch (env('DB_CONNECTION')) {
					case 'sqlite':
						return $timetable->start;
					case 'mysql':
					default:
						$start = DateTime::createFromFormat('Y-m-d', $timetable->start);
						return $start->format('d.m.Y');
				}
			})
			->editColumn('end', function ($timetable) {
				switch (env('DB_CONNECTION')) {
					case 'sqlite':
						return $timetable->end;
					case 'mysql':
					default:
						$end = DateTime::createFromFormat('Y-m-d', $timetable->end);
						return $end->format('d.m.Y');
				}
			})
			->addColumn('action', function ($timetable) {
				$editRoute = route('timetables.edit', ['timetable' => $timetable->id, 'sid' => session()->getId()]);
				$showRoute = route('timetables.show', ['timetable' => $timetable->id, 'sid' => session()->getId()]);
				$actions = '';

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
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$timetable->id}, '')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";
				return $actions;
			})
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function index(Request $request)
	{
		$internship = Internship::findOrFail($request->internship);
		$count = $internship->timetables()->count();

		return view('timetables.index', compact('internship', 'count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function create(Request $request)
	{
		$internship = Internship::findOrFail($request->internship);
		return view('timetables.create', compact('internship'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreTimetableRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreTimetableRequest $request)
	{
		$timetable = Timetable::create($request->all());
		$timetable->save();
		$name = $timetable->name;

		session()->put('success', "Запись графика стажировки " . ($name ? "\"{$name}\" " : "") . "создана");
		return redirect()->route('timetables.index', ['internship' => $timetable->internship->getKey(), 'sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(Request $request, int $id)
	{
		return $this->edit($request, $id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(Request $request, int $id, bool $show = false)
	{
		$timetable = Timetable::findOrFail($id);
		return view('timetables.edit', compact('timetable', 'show'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateTimetableRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateTimetableRequest $request, $id)
	{
		switch (env('DB_CONNECTION')) {
			case 'sqlite':
				break;
			case 'mysql':
			default:
				$start = DateTime::createFromFormat('d.m.Y', $request->start);
				$request->start = $start->format('Y-m-d');

				$end = DateTime::createFromFormat('d.m.Y', $request->end);
				$request->end = $end->format('Y-m-d');
				break;
		}

		$timetable = Timetable::findOrFail($id);
		$name = $timetable->name;
		$timetable->update($request->all());

		session()->put('success', "Запись графика стажировки " . ($name ? "\"{$name}\" " : "") . "обновлена");
		return redirect()->route('timetables.index', ['internship' => $timetable->internship->getKey(), 'sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $timetable
	 * @return bool
	 */
	public function destroy(Request $request, int $timetable)
	{
		if ($timetable == 0) {
			$id = $request->id;
		} else $id = $timetable;

		$timetable = Timetable::findOrFail($id);
		$name = $timetable->iname;
		$timetable->delete();

		event(new ToastEvent('success', '', "Запись графика стажировки " . ($name ? "\"{$name}\" " : "") . "удалена"));
		return true;
	}
}
