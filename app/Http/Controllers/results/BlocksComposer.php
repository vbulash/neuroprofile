<?php

namespace App\Http\Controllers\results;

use App\Models\Block;
use App\Models\BlockType;
use App\Models\FMPType;
use App\Models\History;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Генератор коллекции блоков описания для отображения / писем
 */
class BlocksComposer {
	private History $history;
	private array $area;

	/**
	 * @param int|History $history_id ID или объект записи истории
	 */
	public function __construct(int|History $history_id) {
		if ($history_id instanceof History)
			$this->history = $history_id;
		else
			$this->history = History::findOrFail($history_id);

		$test = $this->history->test;
		$content = json_decode($test->content);
		$this->area = [
			BlocksArea::SHOW->value => $content->descriptions->show ?? null,
			BlocksArea::CLIENT->value => $content->descriptions->client ?? null,
			BlocksArea::MAIL->value => $content->descriptions->mail ?? null
		];
	}

	/**
	 * @param BlocksArea $area
	 */
	public function getProfile(BlocksArea $area): ?Profile {
		$fmptype_id = $this->area[$area->value];
		if (!isset($fmptype_id))
			return null;

		$fmptype = FMPType::findOrFail($fmptype_id);
		return $fmptype->profiles
			->where('code', $this->history->code)
			->first();
	}

	/**
	 * Формирование упорядоченной коллекции блоков
	 *
	 * @return Collection|null Коллекция блоков
	 */
	public function getBlocks(Profile $profile): ?Collection {
		$result = new Collection();
		foreach ($profile->blocks as $block) {
			if ($block->type == BlockType::Alias->value) {
				$sort_no = $block->sort_no;
				$block = $block->parent;
				$block->sort_no = $sort_no;
			}
			$result->add($block);
		}
		return $result->sortBy(['sort_no', 'id']);
	}
}