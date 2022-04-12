<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Support\PermissionUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Exception;

class Step1Controller extends Controller
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
		$query = Employer::all();
		if ($request->has('ids'))
			$query = $query->whereIn('id', $request->ids);

		return Datatables::of($query)
			->addColumn('action', function ($employer) {
				$showRoute = route('e2s.start_internship.step1.show', ['employer' => $employer->id, 'sid' => session()->getId()]);
				$selectRoute = route('e2s.start_internship.step1.select', ['employer' => $employer->id, 'sid' => session()->getId()]);
				$actions = '';

				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
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

	// Выбор
	public function select(int $id)
	{
		$employer = Employer::findOrFail($id);
		session()->forget('context');
		session()->put('context', ['employer' => $employer]);

		return redirect()->route('e2s.start_internship.step2', ['sid' => session()->getId()]);
	}

	// Просмотр анкеты работодателя
	public function showEmployer(int $id)
	{
		$employer = Employer::findOrFail($id);
		return view('services.e2s.start_internship.show-employer', compact('employer'));
	}

	//
	public function run()
	{
		session()->forget('context');
		$view = 'services.e2s.start_internship.step1';
		$count = Employer::all()->count();

		if ($count == 0) {
			event(new ToastEvent('info', '', 'Нет записей работодателей. Необходимо их создать'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}

		if (Auth::user()->can('employers.list')) {
			return view($view, compact('count'));
		} elseif (PermissionUtils::can('employers.list.')) {
			$ids = PermissionUtils::getPermissionIDs('employers.list.');
			return view($view, compact('count', 'ids'));
		} else {
			event(new ToastEvent('info', '', 'Недостаточно прав для выбора работодателя'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}
}
