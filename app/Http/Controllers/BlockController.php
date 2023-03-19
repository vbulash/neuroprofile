<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\blocks\AliasController;
use App\Http\Controllers\blocks\ImageController;
use App\Http\Controllers\blocks\TextController;
use App\Http\Requests\StoreBlockRequest;
use App\Models\Block;
use App\Models\BlockKind;
use App\Models\BlockType;
use App\Models\Profile;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\DataTables;

class BlockController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$context = session('context');
		$profile = Profile::findOrFail($context['profile']);
		$blocks = $profile->blocks->sortBy('sort_no');

		$count = $blocks->count();
		$first = $last = 0;
		if ($count > 1) {
			$first = $blocks->first()->getKey();
			$last = $blocks->last()->getKey();
		}

		return Datatables::of($blocks)
			->addColumn('type', fn($block) => BlockType::getName($block->type))
			->addColumn('action', function ($block) use ($first, $last, $count) {
				$editRoute = route('blocks.edit', ['block' => $block->getKey()]);
				$showRoute = route('blocks.show', ['block' => $block->getKey()]);

				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-pencil-alt', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete({$block->getKey()}, '{$block->name}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];

				if ($block->type == BlockType::Alias->value) {
					$items[] = ['type' => 'divider'];
					$items[] = ['type' => 'item', 'click' => "clickUnlink({$block->getKey()}, '{$block->name}')", 'icon' => 'fas fa-unlink', 'title' => 'Разрыв связи'];
				}

				$actions = createDropdown('Действия', $items);
				$actions .= '<div class="me-4"></div>';

				if ($count > 1) {
					if ($block->getKey() != $first)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выше\" onclick=\"clickUp({$block->getKey()})\">\n" .
							"<i class=\"fas fa-arrow-up\"></i>\n" .
							"</a>\n";
					if ($block->getKey() != $last)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Ниже\" onclick=\"clickDown({$block->id})\">\n" .
							"<i class=\"fas fa-arrow-down\"></i>\n" .
							"</a>\n";
				}

				return $actions;
			})
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index() {
		$context = session('context');
		unset($context['block']);
		session()->put('context', $context);

		$profile = Profile::findOrFail($context['profile']);
		$count = $profile->blocks->count();
		return view('blocks.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return RedirectResponse
	 */
	public function create(Request $request) {
		return match (intval($request->type)) {
			BlockType::Text->value => redirect()->route('texts.create'),
			BlockType::Image->value => redirect()->route('images.create'),
			BlockType::Alias->value => redirect()->route('aliases.create'),
		};
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreBlockRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreBlockRequest $request) {
		$block = match (intval($request->type)) {
			BlockType::Text->value => TextController::store($request->except('_token')),
			BlockType::Image->value => ImageController::store($request),
			BlockType::Alias->value => AliasController::store($request->except('_token')),
		};
		// Перенумеровать блоки
		$blocks = $block->profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($blocks);
		$name = $block->getAttribute('name');

		session()->put('success',
			BlockType::getName($block->type) .
			" &laquo;{$name}&raquo; создан.<br/>Блоки перенумерованы");
		return redirect()->route('blocks.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function show(Request $request, int $id) {
		return $this->edit($request, $id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @param bool $show
	 * @return RedirectResponse
	 */
	public function edit(Request $request, int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$block = Block::findOrFail($id);
		return match (intval($block->type)) {
			BlockType::Text->value => redirect()->route('texts.edit', [
				'text' => $block->getKey(),
				'kind' => $request->has('kind') ? $request->kind : BlockKind::Block->value,
				'mode' => $mode
			]),
			BlockType::Image->value => redirect()->route('images.edit', [
				'image' => $block->getKey(),
				'kind' => $request->has('kind') ? $request->kind : BlockKind::Block->value,
				'mode' => $mode
			]),
			BlockType::Alias->value => redirect()->route('aliases.edit', [
				'alias' => $block->getKey(),
				'kind' => $request->has('kind') ? $request->kind : BlockKind::Block->value,
				'mode' => $mode
			]),
		};
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreBlockRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(Request $request, int $id) {
		$kind = $request->has('kind') ? $request->kind : BlockKind::Block->value;
		$name = match (intval($request->type)) {
			BlockType::Text->value => TextController::update($request->except('_token'), $id),
			BlockType::Image->value => ImageController::update($request, $id),
			BlockType::Alias->value => AliasController::update($request->except('_token'), $id),
			default => 'dashboard'
		};
		// Перенумеровать блоки
		$block = Block::findOrFail($id);
		$blocks = $block->profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($blocks);

		session()->put('success',
			BlockType::getName($block->type) .
			" &laquo;{$name}&raquo; обновлён.<br/>Блоки перенумерованы");

		$route = match (strval($kind)) {
			BlockKind::Block->value => 'blocks.index',
			BlockKind::Parent->value => 'parents.index',
			BlockKind::Kid->value => 'kids.index',
			default => 'dashboard'
		};
		return redirect()->route($route);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $block
	 * @return bool
	 */
	public function destroy(Request $request, int $block) {
		if ($block == 0) {
			$id = $request->id;
		} else
			$id = $block;

		$block = Block::findOrFail($id);
		$profile = $block->profile;
		$name = $block->name;

		try {
			$temp = match (intval($block->type)) {
				BlockType::Text->value => TextController::destroy($block->getKey()),
				BlockType::Image->value => ImageController::destroy($block->getKey()),
				BlockType::Alias->value => AliasController::destroy($block->getKey())
			};

			// Перенумеровать блоки
			$blocks = $profile->blocks
				->sortBy('sort_no')
				->pluck('id')
				->toArray();
			$this->reorder($blocks);

			event(new ToastEvent('success', '',
				BlockType::getName($block->type) .
				" &laquo;{$name}&raquo; удалён.<br/>Блоки перенумерованы"
			));
			return true;
		} catch (QueryException $exception) {
			event(new ToastEvent('error', '',
				"Невозможно удаление блока-предка &laquo;{$name}&raquo; удалён.<br/>" .
				"Удалите блоки, ссылающиеся на него по ссылке" // TODO реализовать интерфейс показа блоков-потомков
			));
			return false;
		}
	}

	private function reorder(array $ids): void {
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

	private function move(int $id, bool $up) {
		$block = Block::findOrFail($id);
		$blocks = $block->profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();

		$currentPos = array_search($block->getKey(), $blocks);
		$targetPos = ($up ? $currentPos - 1 : $currentPos + 1);
		$buffer = $blocks[$targetPos];
		$blocks[$targetPos] = $blocks[$currentPos];
		$blocks[$currentPos] = $buffer;

		$this->reorder($blocks);
	}

	/**
	 * Move question up on sort order
	 *
	 * @param Request $request
	 * @param int $id
	 * @return bool
	 */
	public function up(Request $request) {
		$this->move($request->id, true);

		return true;
	}

	/**
	 * Move question down on sort order
	 *
	 * @param Request $request
	 * @param int $id
	 * @return bool
	 */
	public function down(Request $request) {
		$this->move($request->id, false);

		return true;
	}
}