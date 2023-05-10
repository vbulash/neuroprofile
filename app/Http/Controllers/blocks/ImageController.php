<?php

namespace App\Http\Controllers\blocks;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlockRequest;
use App\Models\Block;
use App\Models\BlockKind;
use App\Models\BlockType;
use App\Models\FileLink;
use App\Models\Profile;
use App\Models\Question;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function GuzzleHttp\Promise\exception_for;

class ImageController extends Controller {
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create() {
		$mode = config('global.create');
		$context = session('context');
		$profile = Profile::findOrFail($context['profile']);
		return view('blocks.image.create', compact('profile', 'mode'));
	}

	/**
	 * Не маршрут!
	 *
	 * @param Request $request
	 */
	public static function store(Request $request): Block {
		$data = $request->except('_token');

		$mediaPath = Block::uploadImage($request, 'full');
		if ($mediaPath)
			FileLink::link($mediaPath);
		$data['full'] = $mediaPath;

		$block = Block::create($data);
		$block->sort_no = config('global.mysql-int-max');

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
	public function edit(Request $request, int $id) {
		$mode = $request->mode;
		$block = Block::findOrFail($id);
		$kind = $request->has('kind') ? $request->kind : BlockKind::Block->value;
		$prev = $request->prev;
		$next = $request->next;
		return view('blocks.image.edit', compact('block', 'mode', 'kind', 'prev', 'next'));
	}

	/**
	 * Не маршрут!
	 *
	 * @param Request $request
	 * @param int $id
	 * @param array $params
	 */
	public static function update(Request $request, int $id, array $params = []): string {
		$block = Block::findOrFail($id);
		$name = $block->name;

		$data = $request->except('_token');

		if ($request->has('full')) {
			$mediaPath = Block::uploadImage($request, 'full', $block->full);
			if ($mediaPath)
				FileLink::link($mediaPath);
			$data['full'] = $mediaPath;
		}

		$data = array_merge($data, $params);
		$block->update($data);
		return $name;
	}

	/**
	 * Не маршрут!
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public static function destroy($id): bool {
		$block = Block::findOrFail($id);
		return $block->delete();
	}
}