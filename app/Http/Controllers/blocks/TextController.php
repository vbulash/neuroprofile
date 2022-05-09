<?php

namespace App\Http\Controllers\blocks;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlockRequest;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\Profile;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TextController extends Controller
{
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create()
	{
		$mode = config('global.create');
		$context = session('context');
		$profile = Profile::findOrFail($context['profile']);
		return view('blocks.text.create', compact('profile', 'mode'));
	}

	/**
	 * Не маршрут!
	 *
	 * @param StoreBlockRequest $request
	 */
	public static function store(array $data): Block
	{
		$block = Block::create($data);
		$block->save();
		return $block;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function edit(Request $request, int $id)
	{
		$mode = $request->mode;
		$block = Block::findOrFail($id);
		return view('blocks.text.edit', compact('block', 'mode'));
	}

	/**
	 * Не маршрут!
	 *
	 * @param array $params
	 * @param int $id
	 */
	public static function update(array $params, int $id): string
	{
		$block = Block::findOrFail($id);
		$name = $block->name;
		$block->update($params);
		return $name;
	}

    /**
     * Не маршрут!
     *
     * @param  int  $id
     * @return bool
     */
    public static function destroy($id): bool
    {
		$block = Block::findOrFail($id);
		$block->delete();
		return true;
    }
}
