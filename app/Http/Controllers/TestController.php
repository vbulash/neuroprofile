<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\tests\StepController;
use App\Models\Test;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

class TestController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$tests = Test::all();

		return Datatables::of($tests)
			->addColumn('contract', fn($test) => sprintf("%s (%s)", $test->contract->number, $test->contract->client->name ))
			->addColumn('set', fn ($test) => $test->set->name)
			->addColumn('action', function ($test) {
				$editRoute = route('tests.edit', ['test' => $test->getKey()]);
				$showRoute = route('tests.show', ['test' => $test->getKey()]);
				$actions = '';

				$actions .=
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-pencil-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$test->getKey()}, '{$test->name}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
	 */
    public function index()
    {
		StepController::clearCurrentStep();
		session()->forget('heap');

		$count = Test::all()->count();
		return view('tests.index', compact('count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return RedirectResponse
	 */
    public function create()
    {
		return redirect()->route('steps.play', [
			'mode' => config('global.create'),
			'test' => 0
		]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return RedirectResponse
	 */
    public function show(int $id): RedirectResponse
	{
        return $this->edit($id, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
	 * @param
     * @return RedirectResponse
	 */
    public function edit(int $id, bool $show = false): RedirectResponse
	{
		$mode = $show ? config('global.show') : config('global.edit');
		return redirect()->route('steps.play', [
			'mode' => $mode,
			'test' => $id
		]);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $test
	 * @return bool
	 */
	public function destroy(Request $request, int $test)
	{
		if ($test == 0) {
			$id = $request->id;
		} else $id = $test;

		$test = Test::findOrFail($id);
		$name = $test->name;
		$test->delete();

		event(new ToastEvent('success', '', "Тест '{$name}' удалён"));
		return true;
	}

	public function list(Request $request)
	{
		return json_encode(DB::select(<<<EOS
SELECT t.id as id, t.name as name, c.mkey as mkey, t.`key` as test
FROM tests as t, contracts as c
WHERE t.contract_id = c.id
ORDER BY t.name
EOS
		));
	}
}
