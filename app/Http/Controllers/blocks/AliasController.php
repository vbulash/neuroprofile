<?php

namespace App\Http\Controllers\blocks;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Profile;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AliasController extends Controller
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
		$blocks = Block::whereNull('block_id');

		return Datatables::of($blocks)
			// id
			// name
			->addColumn('linked', fn($block) => $block->children->count())
			->addColumn('fmptype', fn($block) => $block->profile->fmptype->name)
			->addColumn('profile', fn($block) => $block->profile->getTitle())
			->addColumn('action', function ($block) use ($profile) {
				$showRoute = route('aliases.show', [
					'block' => $block->getKey(),
					'parent' => true,
					'sid' => session()->getId()
				]);
				/*
				$linkRoute = route('blocks.link', [
					'parent' => $block->getKey(),
					'profile' => $profile->getKey(),
					'sid' => session()->getId()
				]);
				*/
				$actions = '';

				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				/*
				$actions .=
					"<a href=\"{$linkRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Связать новый блок с текущим\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				*/

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
		$count = Block::whereNull('block_id')->count();
		return view('blocks.alias.index', compact('count'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $block
	 * @return Application|Factory|View
	 */
	public function show(Request $request, int $block)
	{
		$mode = config('global.show');
		$block = Block::findOrFail($block);
		$parent = $request->has('parent') ? $request->parent : false;
		$view = $parent ? 'blocks.alias.parent' : 'blocks.alias.show';

		if(!$parent) {
			$block = Block::findOrFail($block->getKey());	// Работать не с самими ссылочным блоком, а с его родителем
		}
		return view($view, compact('block', 'mode'));
	}
}
