<?php

namespace App\Http\Controllers\blocks;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Block;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KidController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$context = session('context');
		$parent = $context['parent'];
		$blocks = Block::where('block_id', $parent)->get();

		return Datatables::of($blocks)
			->editColumn('name', fn($block) => $block->getTitle())
			->addColumn('action', function ($block) {
				$actions = '';

				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left ms-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Разрыв связи\" onclick=\"clickUnlink({$block->getKey()}, '{$block->name}')\">\n" .
					"<i class=\"fas fa-unlink\"></i>\n" .
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
		$context = session('context');
		$parent = $context['parent'];
		$count = Block::where('block_id', $parent)->count();
		return view('kids.index', compact('count'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $kid
	 * @return bool
	 */
	public function unlink(Request $request, int $kid)
	{
		if ($kid == 0) {
			$id = $request->id;
		} else $id = $kid;

		$kid = Block::findOrFail($id);
		$name = $kid->name;
		$parent = $kid->parent;
		$kid->type = $parent->type;
		$kid->full = $parent->full;
		$kid->parent()->dissociate();
		$kid->update();

		event(new ToastEvent('success', '', "Блок '{$name}' стал самостоятельным"));
		return true;
	}
}
