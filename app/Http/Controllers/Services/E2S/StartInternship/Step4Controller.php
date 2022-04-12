<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Internship;
use App\Models\Student;
use App\Models\Timetable;
use App\Support\PermissionUtils;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Exception;

class Step4Controller extends Controller
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
		$query = Student::all();

		return Datatables::of($query)
			->editColumn('fio', function ($student) {
				return $student->getTitle();
			})
			->editColumn('birthdate', function ($student) {
				switch (env('DB_CONNECTION')) {
					case 'sqlite':
						return $student->birthdate;
					case 'mysql':
					default:
						$birthdate = DateTime::createFromFormat('Y-m-d', $student->birthdate);
						return $birthdate->format('d.m.Y');
				}
			})
			->addColumn('action', function ($student) {
				$showRoute = route('e2s.start_internship.step4.show', ['student' => $student->id, 'sid' => session()->getId()]);
				$selectRoute = route('e2s.start_internship.step4.select', ['student' => $student->id, 'sid' => session()->getId()]);
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
		$student = Student::findOrFail($id);
		$context['student'] = $student;

		session()->forget('context');
		session()->put('context', $context);

		return redirect()->route('e2s.start_internship.step5', ['sid' => session()->getId()]);
	}

	// Просмотр карточки стажировки
	public function showStudent(int $id)
	{
		$student = Student::findOrFail($id);
		return view('services.e2s.start_internship.show-student', compact('student'));
	}

	//
	public function run()
	{
		$context = session('context');
		unset($context['student']);

		$view = 'services.e2s.start_internship.step4';
		$count = Student::all()->count();

		if ($count == 0) {
			event(new ToastEvent('info', '',
				'Нет студентов. Необходимо их создать'));
			//return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}

		return view($view, compact('count'));
	}
}
