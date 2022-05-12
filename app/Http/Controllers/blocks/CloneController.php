<?php

namespace App\Http\Controllers\blocks;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlockRequest;
use App\Models\Block;
use App\Models\BlockType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;

class CloneController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse
	{
		$blocks = Block::all();

		return Datatables::of($blocks)
			// id
			// name
			->addColumn('fmptype', fn($block) => $block->profile->fmptype->name)
			->addColumn('profile', fn($block) => $block->profile->getTitle())
			->addColumn('action', function ($block) {
				$showRoute = route('clones.show', [
					'mode' => config('global.show'),
					'block' => $block->getKey(),
					'sid' => session()->getId()
				]);
				$cloneRoute = route('clones.clone', [
					'source' => $block->getKey(),
					'sid' => session()->getId()
				]);
				$actions = '';

				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$cloneRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Клонировать блок\">\n" .
					"<i class=\"fas fa-clone\"></i>\n" .
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
		$count = Block::all()->count();
		return view('clones.index', compact('count'));
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
		return view('clones.show', compact('mode', 'block'));
	}

	/**
	 * Клонирование нового блока из блока-источника
	 *
	 * @param int $source
	 * @return RedirectResponse
	 */
	public function clone(int $source)
	{
		// Текущий контекст
		$from = Block::findOrFail($source);
		// Целевой контекст
		$context = session('context');
		// Клонирование из источника
		$to = $from->replicate()->fill([
			'sort_no'=> PHP_INT_MAX,
			'profile_id' => $context['profile']
		]);
		$to->save();

		// Перенумеровать блоки
		$blocks = $to->profile->blocks
			->sortBy('sort_no')
			->pluck('id')
			->toArray();
		$this->reorder($blocks);
		$name = $to->getAttribute('name');

		session()->put('success',
			BlockType::getName($to->type) .
			" &laquo;{$name}&raquo; создан клонированием.<br/>Блоки перенумерованы");
		return redirect()->route('blocks.edit', ['block' => $to->getKey(), 'sid' => session()->getId()]);
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
