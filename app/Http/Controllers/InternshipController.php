<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreInternshipRequest;
use App\Http\Requests\UpdateInternshipRequest;
use App\Models\Employer;
use App\Models\Internship;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use \Exception;

class InternshipController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @param int $employer
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request, int $employer)
	{
		$query = Employer::findOrFail($employer)->internships()->get();

		return Datatables::of($query)
			->editColumn('itype', function ($internship) {
				switch ($internship->itype) {
					case 'Открытая стажировка':
						return 'Открытая';
					case 'Закрытая стажировка':
						return 'Закрытая';
				}
				return '';
			})
			->addColumn('action', function ($internship) {
				$editRoute = route('internships.edit', ['internship' => $internship->id, 'sid' => session()->getId()]);
				$showRoute = route('internships.show', ['internship' => $internship->id, 'sid' => session()->getId()]);
				$timetablesRoute = route('timetables.index', ['internship' => $internship->id, 'sid' => session()->getId()]);
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
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$internship->id}, '{$internship->iname}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$timetablesRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"График(и) стажировок\">\n" .
					"<i class=\"fas fa-clock\"></i>\n" .
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
		$employer = Employer::findOrFail($request->employer);
		$count = $employer->internships()->count();

		return view('internships.index', compact('employer', 'count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function create(Request $request)
	{
		$employer = Employer::findOrFail($request->employer);
		return view('internships.create', compact('employer'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreInternshipRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreInternshipRequest $request)
	{
		$internship = Internship::create($request->all());
		$internship->save();
		$name = $internship->iname;

		session()->put('success', "Стажировка \"{$name}\" создана");
		return redirect()->route('internships.index', ['employer' => $internship->employer->getKey(), 'sid' => session()->getId()]);
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
		$internship = Internship::findOrFail($id);
		return view('internships.edit', compact('internship', 'show'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateInternshipRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateInternshipRequest $request, $id)
	{
		$internship = Internship::findOrFail($id);
		$name = $internship->iname;
		$internship->update($request->all());

		session()->put('success', "Стажировка \"{$name}\" обновлена");
		return redirect()->route('internships.index', ['employer' => $internship->employer->getKey(), 'sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $internship
	 * @return bool
	 */
	public function destroy(Request $request, int $internship)
	{
		if ($internship == 0) {
			$id = $request->id;
		} else $id = $internship;

		$internship = Internship::findOrFail($id);
		$name = $internship->iname;
		$internship->delete();

		event(new ToastEvent('success', '', "Стажировка '{$name}' удалена"));
		return true;
	}
}
