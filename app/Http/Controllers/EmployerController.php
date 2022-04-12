<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreEmployerRequest;
use App\Models\Employer;
use App\Models\User;
use App\Support\PermissionUtils;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use \Exception;

class EmployerController extends Controller
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
		if($request->has('ids'))
			$query = $query->whereIn('id', $request->ids);

		return Datatables::of($query)
			->editColumn('link', function ($employer) {
				return $employer->user->name;
			})
			->addColumn('action', function ($employer) {
				$editRoute = route('employers.edit', ['employer' => $employer->id, 'sid' => session()->getId()]);
				$showRoute = route('employers.show', ['employer' => $employer->id, 'sid' => session()->getId()]);
				$internshipRoute = route('internships.index', ['employer' => $employer->id, 'sid' => session()->getId()]);
				$actions = '';

				if (Auth::user()->can('employers.edit'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('employers.show'))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('employers.destroy')) {
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$employer->id}, '{$employer->name}')\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				}

				if (Auth::user()->can('employers.edit'))
					$actions .=
						"<a href=\"{$internshipRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Стажировки\">\n" .
						"<i class=\"fas fa-calendar-alt\"></i>\n" .
						"</a>\n";
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
		$count = Employer::all()->count();
		if(Auth::user()->can('employers.list')) {
			return view('employers.index', compact('count'));
		} elseif (PermissionUtils::can('employers.list.')) {
			$ids = PermissionUtils::getPermissionIDs('employers.list.');
			return view('employers.index', compact('count', 'ids'));
		} elseif (Auth::user()->can('employers.create')) {
			return redirect()->route('employers.create', ['sid' => session()->getId()]);
		} else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи работодателя'));
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
		$baseRight = "employers.create";
		if (Auth::user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()->pluck('name', 'id')->toArray();
			return view('employers.create', compact('users', 'show'));
		} elseif (Auth::user()->can($baseRight))
			return view('employers.create', compact('show'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи работодателя'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreEmployerRequest $request
     * @return RedirectResponse
     */
    public function store(StoreEmployerRequest $request)
    {
		$employer = Employer::create($request->all());
		$employer->save();
		$name = $employer->name;

		$permissions = [
			'employers.list',
			'employers.edit',
			'employers.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $employer->getKey());
			$employer->user->givePermissionTo($perm);
		}

		session()->put('success', "Работодатель \"{$name}\" создан");
		return redirect()->route('employers.index', ['sid' => session()->getId()]);
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
		$employer = Employer::findOrFail($id);
		$baseRight = sprintf("employers.%s", $show ? "show" : "edit");
		$right = sprintf("%s.%d", $baseRight, $employer->getKey());
		if (Auth::user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()->pluck('name', 'id')->toArray();
			return view('employers.edit', compact('employer', 'users', 'show'));
		} elseif (Auth::user()->can($baseRight) || Auth::user()->can($right))
			return view('employers.edit', compact('employer', 'show'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для редактирования / просмотра записи работодателя'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreEmployerRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
    public function update(StoreEmployerRequest $request, $id)
    {
		$employer = Employer::findOrFail($id);
		$name = $employer->name;
		$employer->update($request->all());

		$permissions = [
			'employers.list',
			'employers.edit',
			'employers.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $employer->getKey());
			$employer->user->givePermissionTo($perm);
		}

		session()->put('success', "Анкета работодателя \"{$name}\" обновлена");
		return redirect()->route('employers.index', ['sid' => session()->getId()]);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $employer
	 * @return bool
	 */
	public function destroy(Request $request, int $employer)
    {
        if ($employer == 0) {
            $id = $request->id;
        } else $id = $employer;

        $employer = Employer::findOrFail($id);
        $name = $employer->name;
        $employer->delete();

        event(new ToastEvent('success', '', "Работодатель '{$name}' удалён"));
        return true;
    }
}
