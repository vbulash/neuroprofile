<?php

namespace App\Http\Controllers\blocks;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\BlockKind;
use App\Models\BlockType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
			->editColumn('name', fn ($block) => $block->getTitle())
			->editColumn('fmptype', fn ($block) => $block->profile->fmptype->getTitle())
			->editColumn('profile', fn ($block) => $block->profile->getTitle())
			->addColumn('type', fn ($block) => BlockType::getName($block->type))
			->addColumn('action', function ($block) {
				$editRoute = route('kids.edit', ['kid' => $block->getKey(), 'sid' => session()->getId()]);
				$actions = '';

				$actions .=
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-pencil-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
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

		event(new ToastEvent('success', '',
			"Блок &laquo;{$name}&raquo; стал самостоятельным<br/>" .
			"<a href='" . route('kids.follow', ['kid' => $kid->getKey()]) .
			"'>Редактировать самостоятельный блок</a> ?"
		));
		return true;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return RedirectResponse
	 */
	public function edit(int $id, bool $show = false)
	{
		$block = Block::findOrFail($id);
		return redirect()->route('blocks.edit', [
			'block' => $block->getKey(),
			'kind' => BlockKind::Kid->value,
			'sid' => session()->getId()
		]);
	}

	public function follow(int $id)
	{
		$block = Block::findOrFail($id);

		$context = session('context');
		$context['fmptype'] = $block->profile->fmptype->getKey();
		$context['profile'] = $block->profile->getKey();
		session()->put('context', $context);

		return redirect()->route('blocks.edit', [
			'block' => $block->getKey(),
			'kind' => BlockKind::Block->value,
			'sid' => session()->getId()
		]);
	}
}
