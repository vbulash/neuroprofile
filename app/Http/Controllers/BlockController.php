<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\blocks\AliasController;
use App\Http\Controllers\blocks\ImageController;
use App\Http\Controllers\blocks\TextController;
use App\Http\Requests\StoreBlockRequest;
use App\Models\Block;
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
use Yajra\DataTables\DataTables;

class BlockController extends Controller
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
				$editRoute = route('blocks.edit', ['block' => $block->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('blocks.show', ['block' => $block->getKey(), 'sid' => session()->getId()]);
				$actions = '';

				$actions .=
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-pencil-alt\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$block->getKey()}, '{$block->name}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				if ($count > 1) {
					if ($block->getKey() != $first)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
							"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выше\" onclick=\"clickUp({$block->getKey()})\">\n" .
							"<i class=\"fas fa-arrow-up\"></i>\n" .
							"</a>\n";
					if ($block->getKey() != $last)
						$actions .=
							"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
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
	public function index()
	{
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
	public function create(Request $request)
	{
		return match (intval($request->type)) {
			BlockType::Text->value => redirect()->route('texts.create', ['sid' => session()->getId()]),
			BlockType::Image->value => redirect()->route('images.create', ['sid' => session()->getId()]),
			BlockType::Alias->value => redirect()->route('aliases.create', ['sid' => session()->getId()]),
		};
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreBlockRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreBlockRequest $request)
	{
		$block = match(intval($request->type)) {
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
		return redirect()->route('blocks.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function show(int $id)
	{
		return $this->edit($id, true);
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
		$mode = $show ? config('global.show') : config('global.edit');
		$block = Block::findOrFail($id);
		return match (intval($block->type)) {
			BlockType::Text->value => redirect()->route('texts.edit', [
				'text' => $block->getKey(),
				'mode' => $mode,
				'sid' => session()->getId()
			]),
			BlockType::Image->value => redirect()->route('images.edit', [
				'image' => $block->getKey(),
				'mode' => $mode,
				'sid' => session()->getId()
			]),
			BlockType::Alias->value => redirect()->route('aliases.edit', [
				'alias' => $block->getKey(),
				'mode' => $mode,
				'sid' => session()->getId()
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
	public function update(Request $request, int $id)
	{
		$name = match(intval($request->type)) {
			BlockType::Text->value => TextController::update($request->except('_token'), $id),
			BlockType::Image->value => ImageController::update($request, $id),
			BlockType::Alias->value => AliasController::update($request->except('_token'), $id),
			default => throw new Exception('Неизвестный тип блока')
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
		return redirect()->route('blocks.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
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

		try {
			$temp = match(intval($block->type)) {
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
				"Удалите блоки, ссылающиеся на него по ссылке"	// TODO реализовать интерфейс показа блоков-потомков
			));
			return false;
		}
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

	private function move(int $id, bool $up)
	{
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
	public function up(Request $request)
	{
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
	public function down(Request $request)
	{
		$this->move($request->id, false);

		return true;
	}
}
