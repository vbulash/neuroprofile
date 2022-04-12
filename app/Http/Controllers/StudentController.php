<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use App\Models\User;
use App\Support\PermissionUtils;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use \Exception;

class StudentController extends Controller
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
		if($request->has('ids'))
			$query = $query->whereIn('id', $request->ids);

		return Datatables::of($query)
			->editColumn('fio', function ($student) {
				return sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));
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
			->editColumn('link', function ($student) {
				return $student->user->name;
			})
			->addColumn('action', function ($student) {
				$editRoute = route('students.edit', ['student' => $student->id, 'sid' => session()->getId()]);
				$showRoute = route('students.show', ['student' => $student->id, 'sid' => session()->getId()]);
				$actions = '';

				if (Auth::user()->can('students.edit'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('students.show'))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('students.destroy')) {
					$name = sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$student->id}, '{$name}')\">\n" .
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
		$count = Student::all()->count();
		if(Auth::user()->can('students.list')) {
			return view('students.index', compact('count'));
		} elseif (PermissionUtils::can('students.list.')) {
			$ids = PermissionUtils::getPermissionIDs('students.list.');
			return view('students.index', compact('count', 'ids'));
		} elseif (Auth::user()->can('students.create')) {
			return redirect()->route('students.create', ['sid' => session()->getId()]);
		} else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи практиканта'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function create()
	{
		$show = false;
		$baseRight = "students.create";
		if (Auth::user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()->pluck('name', 'id')->toArray();
			return view('students.create', compact('users', 'show'));
		} elseif (Auth::user()->can($baseRight))
			return view('students.create', compact('show'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи практиканта'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreStudentRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreStudentRequest $request)
	{
		switch (env('DB_CONNECTION')) {
			case 'sqlite':
				break;
			case 'mysql':
			default:
				$birthdate = DateTime::createFromFormat('d.m.Y', $request->birthdate);
				$request->birthdate = $birthdate->format('Y-m-d');
				break;
		}

		$student = Student::create($request->all());
		$student->save();
		$name = sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));

		$permissions = [
			'students.list',
			'students.edit',
			'students.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $student->getKey());
			$student->user->givePermissionTo($perm);
		}

		session()->put('success', "Практикант \"{$name}\" создан");
		return redirect()->route('students.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(int $id)
	{
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false)
	{
		$student = Student::findOrFail($id);
		$baseRight = sprintf("students.%s", $show ? "show" : "edit");
		$right = sprintf("%s.%d", $baseRight, $student->getKey());
		if (Auth::user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()->pluck('name', 'id')->toArray();
			return view('students.edit', compact('student', 'users', 'show'));
		} elseif (Auth::user()->can($baseRight) || Auth::user()->can($right))
			return view('students.edit', compact('student', 'show'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для редактирования / просмотра записи практиканта'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreStudentRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(StoreStudentRequest $request, int $id)
	{
		switch (env('DB_CONNECTION')) {
			case 'sqlite':
				break;
			case 'mysql':
			default:
				$birthdate = DateTime::createFromFormat('d.m.Y', $request->birthdate);
				$request->birthdate = $birthdate->format('Y-m-d');
				break;
		}

		$student = Student::findOrFail($id);
		$name = sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));
		$student->update($request->all());

		$permissions = [
			'students.list',
			'students.edit',
			'students.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $student->getKey());
			$student->user->givePermissionTo($perm);
		}

		session()->put('success', "Анкета практиканта \"{$name}\" обновлена");
		return redirect()->route('students.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $student
	 * @return bool
	 */
	public function destroy(Request $request, int $student)
	{
		if ($student == 0) {
			$id = $request->id;
		} else $id = $student;

		$student = Student::findOrFail($id);
		$name = sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));
		$student->delete();

		event(new ToastEvent('success', '', "Практикант '{$name}' удалён"));
		return true;
	}
}
