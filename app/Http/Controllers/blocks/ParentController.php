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

class ParentController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
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
			->editColumn('profile', fn($block) => $block->model->profile->getTitle())
			->addColumn('type', fn($block) => BlockType::getName($block->model->type))
			->addColumn('created_at', fn($block) => $block->model->created_at->format('d.m.Y H:i:s'))
			->addColumn('updated_at', fn($block) => $block->model->updated_at->format('d.m.Y H:i:s'))
			->addColumn('action', function ($block) {
				$editRoute = route('parents.edit', [
					'parent' => $block->model->getKey()
				]);
				$showRoute = route('parents.show', [
					'parent' => $block->model->getKey()
				]);
				$selectRoute = route('parents.select', [
					'parent' => $block->model->getKey()
				]);

				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-pencil-alt', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Блоки-потомки'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(int $id) {
		session()->forget('context');
		session()->put('context', ['parent' => $id]);

		return redirect()->route('kids.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index() {
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
	public function show(int $id) {
		$block = Block::findOrFail($id);
		return redirect()->route('blocks.show', [
			'block' => $block->getKey(),
			'kind' => BlockKind::Parent->value
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return RedirectResponse
	 */
	public function edit(int $id, bool $show = false) {
		$block = Block::findOrFail($id);
		return redirect()->route('blocks.edit', [
			'block' => $block->getKey(),
			'kind' => BlockKind::Parent->value
		]);
	}
}