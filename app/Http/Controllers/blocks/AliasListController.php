<?php

namespace App\Http\Controllers\blocks;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\BlockType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AliasListController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$blocks = Block::whereNotNull('block_id');    // Только ссылочные блоки

		return Datatables::of($blocks)
			// id
			// name
			->addColumn('fmptype', fn($block) => $block->profile->fmptype->name)
			->addColumn('profile', fn($block) => $block->profile->getTitle())
			->addColumn('parent', fn ($block) => $block->parent->getKey())
			->addColumn('action', function ($block) {
				$showRoute = route('aliaslists.show', [
					'alias' => $block->getKey(),
					'parent' => false
				]);
				$selectRoute = route('aliaslists.show', [
					'alias' => $block->parent->getKey(),
					'parent' => true,
					'kid' => $block->getKey()
				]);
				$actions = '';

				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$block->getKey()}, '{$block->name}')\">\n" .
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

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index()
	{
		session()->forget('context');
		$count = Block::whereNotNull('block_id')->count();
		return view('aliaslists.index', compact('count'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(Request $request, int $id)
	{
		$mode = config('global.show');
		$block = Block::findOrFail($id);
		$parent = $request->parent;
		if ($parent) {	// Отображаем предка
			session()->put('context', ['alias' => $request->kid]);
		}
		return view('aliaslists.show', compact('mode', 'block', 'parent'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $block
	 * @return bool
	 */
	public function destroy(Request $request, int $block)
	{
		if ($block == 0) {
			$id = $request->id;
		} else $id = $block;

		$block = Block::findOrFail($id);
		$profile = $block->profile;
		$name = $block->name;

		AliasController::destroy($block->getKey());

		// Перенумеровать блоки
		$blocks = $profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($blocks);

		event(new ToastEvent('success', '',
			BlockType::getName($block->type) .
			" &laquo;{$name}&raquo; удалён.<br/>" .
			"Блоки в нейропрофиле &laquo;{{ $profile->getTitle() }}&raquo;перенумерованы"
		));
		return true;
	}

	private function reorder(array $ids): void
	{
		DB::transaction(function () use ($ids) {
			$counter = 0;
			foreach ($ids as $id) {
				$counter++;
				$block = Block::findOrFail($id);
				if ($block->sort_no != $counter)
					$block->update(['sort_no' => $counter]);
			}
		});
	}
}
