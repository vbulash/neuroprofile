<?php

namespace App\Http\Controllers\blocks;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlockRequest;
use App\Models\Block;
use App\Models\BlockKind;
use App\Models\Profile;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
		$blocks = DB::select(<<<SQL
SELECT
    blocks.id as id,
    blocks.name as name,
    fmptypes.name as fmptype,
    profiles.name as profile,
    fmptypes.ethalon as ethalon
FROM blocks, profiles, fmptypes
WHERE blocks.profile_id = profiles.id AND profiles.fmptype_id = fmptypes.id
    AND blocks.block_id is null AND profiles.code = :code
ORDER BY ethalon DESC, fmptype, profile, name
SQL,
			['code' => $profile->code]);
		foreach ($blocks as &$block) {
			$block = (array) $block;
			$block['model'] = Block::findOrFail($block['id']);
			$block = (object) $block;
		}

		return Datatables::of($blocks)
			->editColumn('profile', fn ($block) => $block->model->profile->getTitle())
			->addColumn('linked', fn ($block) => $block->model->children->count())
			->addColumn('action', function ($block) {
				$showRoute = route('aliases.edit', [
					'mode' => config('global.show'),
					'alias' => $block->id,
					'block_id' => $block->id,
					'sid' => session()->getId()
				]);
				$linkRoute = route('aliases.create', [
					'block_id' => $block->id,
					'sid' => session()->getId()
				]);
				$actions = '';

				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$linkRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Связать новый блок с текущим\">\n" .
					"<i class=\"fas fa-link\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create(Request $request)
	{
		if($request->has('block_id')) {	// Создание ссылочного блока - предок уже выбран
			$block_id = $request->block_id;
			$context = session('context');
			$profile_id = $context['profile'];
			return view('blocks.alias.create', compact('block_id', 'profile_id'));
		} else {	// Выбор предка для нового ссылочного блока
			$count = Block::whereNull('block_id')->count();
			return view('blocks.alias.index', compact('count'));
		}
	}

	/**
	 * Не маршрут!
	 *
	 * @param Request $request
	 */
	public static function store(array $data): Block
	{
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
	public function edit(Request $request, int $id)
	{
		$mode = $request->mode;
		$block = Block::findOrFail($id);
		if ($block->block_id) {    // Редактирование / просмотр ссылочного блока
			$view = 'blocks.alias.edit';
		} else {	// Просмотр предка
			$view = 'blocks.alias.parent';
		}
		$kind = $request->has('kind') ? $request->kind : BlockKind::Block->value;
		return view($view, compact('block', 'mode', 'kind'));
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
		$block->update([
			'name' => $params['name']
		]);
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
		return $block->delete();
	}
}
