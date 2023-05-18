<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\BlockType;
use App\Models\FMPType;
use App\Models\Profile;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Element\Footer;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Language;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;

class ProfileController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(): JsonResponse {
		$context = session('context');
		$fmptype = FMPType::findOrFail($context['fmptype']);
		$profiles = $fmptype->profiles;

		return Datatables::of($profiles)
			->addColumn('fact', fn($profile) => $profile->blocks->count())
			->addColumn('action', function ($profile) {
				$editRoute = route('profiles.edit', ['profile' => $profile->getKey()]);
				$showRoute = route('profiles.show', ['profile' => $profile->getKey()]);
				$selectRoute = route('profiles.select', ['profile' => $profile->getKey()]);

				$items = [];
				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-pencil-alt', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete({$profile->getKey()}, '{$profile->name}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Блоки описания'];
				$items[] = ['type' => 'item', 'click' => "clickExport({$profile->getKey()})", 'icon' => 'fas fa-file-download', 'title' => 'Выгрузка нейропрофиля'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(int $id) {
		$context = session('context');
		$context['profile'] = $id;
		session()->put('context', $context);

		return redirect()->route('blocks.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index() {
		$context = session('context');
		unset($context['profile']);
		unset($context['block']);
		session()->put('context', $context);

		$fmptype = FMPType::findOrFail($context['fmptype']);
		$count = $fmptype->profiles->count();
		$codeCount = count(Profile::getFreeCodes($fmptype->getKey()));
		return view('profiles.index', compact('count', 'codeCount'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create() {
		$mode = config('global.create');
		$context = session('context');
		$fmptype = FMPType::findOrFail($context['fmptype']);
		$codes = Profile::getFreeCodes($fmptype->getKey());
		return view('profiles.create', compact('fmptype', 'mode', 'codes'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreProfileRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreProfileRequest $request) {
		$name = '';
		DB::transaction(function () use ($request, &$name) {
			$context = session('context');

			$profile = Profile::create($request->except('_token'));
			$profile->save();

			$fmptype = FMPType::findOrFail($context['fmptype']);
			$count = $fmptype->profiles->count();
			$fmptype->update([
				'active' => intval($fmptype->limit) == $count
			]);

			$name = $profile->name;
		});

		session()->put('success', "Нейропрофиль \"{$name}\" создан");
		return redirect()->route('profiles.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id) {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$profile = Profile::findOrFail($id);
		$context = session('context');
		$codes = Profile::getAllCodes($context['fmptype']);
		return view('profiles.edit', compact('profile', 'mode', 'codes'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateProfileRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateProfileRequest $request, $id) {
		$profile = Profile::findOrFail($id);
		$name = $profile->name;
		$profile->update($request->except('_token'));

		session()->put('success', "Нейропрофиль \"{$name}\" обновлён");
		return redirect()->route('profiles.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $profile
	 * @return bool
	 */
	public function destroy(Request $request, int $profile) {
		if ($profile == 0) {
			$id = $request->id;
		} else
			$id = $profile;

		$name = '';
		DB::transaction(function () use ($id, &$name) {
			$profile = Profile::findOrFail($id);
			$name = $profile->name;
			$profile->delete();

			$context = session('context');
			$fmptype = FMPType::findOrFail($context['fmptype']);
			$count = $fmptype->profiles->count() - 1; // -1 - в рамках транзакции $profile->delete() не срабатывает мгноваенно
			$fmptype->update([
				'active' => intval($fmptype->limit) == $count
			]);
		});

		event(new ToastEvent('success', '', "Нейропрофиль '{$name}' удалён"));
		return true;
	}

	public function export(Request $request) {
		$profile = $request->profile;
		$_profile = Profile::findOrFail($profile);

		$document = new PhpWord();
		$document->getSettings()->setThemeFontLang(new Language(Language::RU_RU));
		$document->getSettings()->setHideGrammaticalErrors(true);
		$document->getSettings()->setHideSpellingErrors(true);

		$document->addTitleStyle(1,
			[
				'name' => 'Helvetica Neue',
				'size' => 20,
				'bold' => true,
			], [
				// Твипы = pt * 20
				'spaceAfter' => 480,
			]
		);
		$document->addTitleStyle(2,
			[
				'name' => 'Helvetica Neue',
				'size' => 18,
				'bold' => false,
			], [
				// Твипы = pt * 20
				'spaceAfter' => 240,
			]
		);
		$document->addTitleStyle(3,
			[
				'name' => 'Helvetica Neue',
				'size' => 16,
				'bold' => true,
			], [
				// Твипы = pt * 20
				'spaceBefore' => 240,
				'spaceAfter' => 240,
				'keepNext' => true,
			]
		);
		$bold = $document->addFontStyle('bold', ['bold' => true]);

		$section = $document->addSection();
		$title = sprintf("Тип описания \"%s\", нейропрофиль \"%s\" (код \"%s\")", $_profile->fmptype->name, $_profile->name, $_profile->code);

		$section->addTitle($title, 1);
		// $section->addTitle('Выгрузка блоков:', 2);

		foreach ($_profile->blocks->sortBy('sort_no') as $block) {
			$section->addTitle(strlen($block->name) <= 1 ? ' ' : $block->name, 3);
			if ($block->type == BlockType::Alias->value)
				$block = $block->parent;
			switch ($block->type) {
				case BlockType::Text->value:
					$html = str_replace('<br>', ' ', $block->full);
					try {
						Html::addHtml($section, $html);
					} catch (Exception $e) {
						$section->addText(sprintf(
							'Ошибка разметки полного содержимого блока № %d. ' .
							'Сообщение об ошибке - "%s". ' .
							'Обратитесь к разработчикам платформы', $block->getKey(), $e->getMessage()));
					}
					break;
				case BlockType::Image->value:
					$section->addImage(public_path() . '/uploads/' . $block->full);
					break;
			}
		}

		$pages = 'Страница {PAGE} из {NUMPAGES}';

		$footer = $section->addFooter(Footer::FIRST);
		$footer->addPreserveText($pages, $bold, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

		$otherFooter = $section->addFooter();
		$otherFooter->addPreserveText($pages, $bold, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

		$header = $section->addHeader();
		$header->firstPage();
		$header->addText('');

		$otherHeader = $section->addHeader();
		$otherHeader->addText($title, $bold);

		$tmpdoc = 'tmp/' . Str::uuid() . '.docx';
		$writer = IOFactory::createWriter($document, 'Word2007');
		try {
			Storage::makeDirectory('tmp');
			$writer->save(Storage::path($tmpdoc));
			// Название типа описания - Название нейропрофиля (Код нейропрофиля)
			$tempFile = sprintf("Выгрузка нейропрофиля - %s - %s (код %s)", $_profile->fmptype->name, $_profile->name, $_profile->code);
			$tempFile = str_replace([
				' ',
				'.',
				',',
				'\"',
				'\'',
				'\\',
				'/',
				'«',
				'»'
			], '_', $tempFile);
			return response()
				->download(Storage::path($tmpdoc), $tempFile . '.docx')
				->deleteFileAfterSend();
		} catch (Exception $e) {
		}

		event(new ToastEvent('success', '', "Нейропрофиль '{$_profile->name}' экспортирован'"));
		return true;
	}
}