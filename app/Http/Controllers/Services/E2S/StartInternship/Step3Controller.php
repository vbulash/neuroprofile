<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Internship;
use App\Models\Timetable;
use App\Support\PermissionUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Exception;

class Step3Controller extends Controller
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
		$context = session('context');
		$query = $context['internship']->timetables()->get();

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
				$showRoute = route('e2s.start_internship.step3.show', ['timetable' => $timetable->id, 'sid' => session()->getId()]);
				$selectRoute = route('e2s.start_internship.step3.select', ['timetable' => $timetable->id, 'sid' => session()->getId()]);
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
		$context = session('context');
		$timetable = Timetable::findOrFail($id);
		$context['timetable'] = $timetable;

		session()->forget('context');
		session()->put('context', $context);

		return redirect()->route('e2s.start_internship.step4', ['sid' => session()->getId()]);
	}

	// Просмотр карточки стажировки
	public function showTimetable(int $id)
	{
		$timetable = Timetable::findOrFail($id);
		return view('services.e2s.start_internship.show-timetable', compact('timetable'));
	}

	//
	public function run()
	{
		$context = session('context');
		$internship = $context['internship'];
		unset($context['timetable']);
		unset($context['student']);

		$view = 'services.e2s.start_internship.step3';
		$count = $internship->timetables()->count();

		if ($count == 0) {
			event(new ToastEvent('info', '',
				'Нет записей графиков стажировок. Необходимо их создать, либо вернуться на шаг назад и продолжить работу с другой стажировкой'));
			//return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}

		return view($view, compact('internship', 'count'));
	}
}
