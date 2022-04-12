<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Internship;
use App\Support\PermissionUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Exception;

class Step2Controller extends Controller
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
		$query = $context['employer']->internships()->whereNot('status', 'Закрыта')->get();

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
				$showRoute = route('e2s.start_internship.step2.show', ['internship' => $internship->id, 'sid' => session()->getId()]);
				$selectRoute = route('e2s.start_internship.step2.select', ['internship' => $internship->id, 'sid' => session()->getId()]);
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
		$internship = Internship::findOrFail($id);
		$context['internship'] = $internship;

		session()->forget('context');
		session()->put('context', $context);

		return redirect()->route('e2s.start_internship.step3', ['sid' => session()->getId()]);
	}

	// Просмотр карточки стажировки
	public function showInternship(int $id)
	{
		$internship = Internship::findOrFail($id);
		return view('services.e2s.start_internship.show-internship', compact('internship'));
	}

	//
	public function run()
	{
		$context = session('context');
		$employer = $context['employer'];
		unset($context['internship']);
		unset($context['timetable']);
		unset($context['student']);

		$view = 'services.e2s.start_internship.step2';
		$count = $employer->internships()->count();

		if ($count == 0) {
			event(new ToastEvent('info', '',
				'Нет записей стажировок. Необходимо их создать, либо вернуться на шаг назад и продолжить работу с другим работодателем'));
			//return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}

		return view($view, compact('employer', 'count'));
	}
}
