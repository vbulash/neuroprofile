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
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ParentController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$blocks = DB::select(<<<SQL
SELECT
    blocks.id AS id,
    blocks.name AS name,
    fmptypes.name AS fmptype,
    profiles.code AS code,
    profiles.name AS profile
FROM blocks, profiles, fmptypes
WHERE blocks.profile_id = profiles.id AND profiles.fmptype_id = fmptypes.id
    AND blocks.id IN (
    	SELECT DISTINCT block_id
    	FROM blocks
    	WHERE block_id IS NOT NULL
    )
SQL);
		foreach ($blocks as &$block) {
			$block = (array) $block;
			$block['model'] = Block::findOrFail($block['id']);
			$block = (object) $block;
		}

		return Datatables::of($blocks)
			->editColumn('profile', fn ($block) => $block->model->profile->getTitle())
			->addColumn('type', fn($block) => BlockType::getName($block->model->type))
			->addColumn('action', function ($block) {
				$editRoute = route('parents.edit', [
					'parent' => $block->model->getKey(),
					'sid' => session()->getId()
				]);
				$showRoute = route('parents.show', [
					'parent' => $block->model->getKey(),
					'sid' => session()->getId()
				]);
				$selectRoute = route('parents.select', [
					'parent' => $block->model->getKey(),
					'sid' => session()->getId()
				]);
				$actions = '';

				$actions .=
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-pencil-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$block->model->getKey()}, '{$block->model->name}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
					"<i class=\"fas fa-check\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	public function select(int $id)
	{
		session()->forget('context');
		session()->put('context', ['parent' => $id]);

		return redirect()->route('kids.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index()
	{
		session()->forget('context');
		$count = Block::whereNull('block_id')->count();
		return view('parents.index', compact('count'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function show(int $id)
	{
		$block = Block::findOrFail($id);
		return redirect()->route('blocks.show', [
			'block' => $block->getKey(),
			'kind' => BlockKind::Parent->value,
			'sid' => session()->getId()]
		);
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
			'kind' => BlockKind::Parent->value,
			'sid' => session()->getId()
		]);
	}
}
