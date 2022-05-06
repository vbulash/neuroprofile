<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreBlockRequest;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\FMPType;
use App\Models\Profile;
use App\Models\Set;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
		if($count > 1) {
			$first = $blocks->first()->getKey();
			$last = $blocks->last()->getKey();
		}

		return Datatables::of($blocks)
			->addColumn('type', fn($block) => BlockType::getName($block->type))
			->addColumn('action', function ($block) use($first, $last, $count) {
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

				if($count > 1) {
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
     * @return Application|Factory|View
	 */
    public function create(Request $request)
    {
		$type = $request->type;
		$mode = config('global.create');
		$context = session('context');
		$profile = Profile::findOrFail($context['profile']);
		$view = match (intval($type)) {
			BlockType::Text->value => 'blocks.text.create',
			// TODO Реализовать ссылки на create других типов блоков
			default => abort(500)	// Нереализованный тип блока
		};
		return view($view, compact('profile', 'mode'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreBlockRequest $request
	 * @return RedirectResponse
	 */
    public function store(StoreBlockRequest $request)
    {
		$block = Block::create($request->except('_token'));
		$block->save();	// TODO Если будет различие в сохранении различных типов блоков - учесть
		// Перенумеровать блоки
		$blocks = $block->profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($blocks);
		$name = $block->name;

		session()->put('success', BlockType::getName($block->type) . " &laquo;{$name}&raquo; создан.<br/>Блоки перенумерованы");
		return redirect()->route('blocks.index', ['sid' => session()->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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
	 * @param bool $show
     * @return Application|Factory|View
	 */
    public function edit(int $id, bool $show = false)
    {
        $mode = $show ? config('global.show') : config('global.edit');
		$block = Block::findOrFail($id);
		$view = match (intval($block->type)) {
			BlockType::Text->value => 'blocks.text.edit',
			// TODO Реализовать ссылки на edit других типов блоков
			default => abort(500)	// Нереализованный тип блока
		};
		return view($view, compact('block', 'mode'));
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
		$block = Block::findOrFail($id);
		$name = $block->name;
		$block->update($request->except('_token'));
		// Перенумеровать блоки
		$blocks = $block->profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($blocks);

		session()->put('success', BlockType::getName($block->type) . " &laquo;{$name}&raquo; обновлён.<br/>Блоки перенумерованы");
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
		$name = $block->name;
		if($block->type == BlockType::Alias) {
			// TODO Здесь желательно дать список блоков с возможность перехода в соответствующие списки блоков и удаления блоков-потомков
			event(new ToastEvent('error', '', "Нельзя удалить блок &laquo;{$name}&raquo; - на него есть ссылки других блоков"));
			return false;
		}
		$block->delete();
		// Перенумеровать блоки
		$blocks = $block->profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($blocks);

		event(new ToastEvent('success', '', "Нейропрофиль &laquo;{$name}&raquo; удалён.<br/>Блоки перенумерованы"));
		return true;
	}

	private function reorder(array $ids): void
	{
		DB::transaction(function () use ($ids) {
			$counter = 0;
			foreach ($ids as $id) {
				$counter++;
				$block = Block::findOrFail($id);
				if($block->sort_no != $counter)
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
