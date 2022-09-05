<?php

namespace App\Http\Controllers\results;

use App\Models\Block;
use App\Models\FMPType;
use App\Models\History;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Collection;

/**
 * Генератор коллекции блоков описания для отображения / писем
 */
class BlocksComposer
{
	private History $history;
	public Profile $profile;
	private array $area;

	/**
	 * @param int|History $history_id ID записи истории
	 */
	public function __construct(int|History $history_id)
	{
		if ($history_id instanceof History) $this->history = $history_id;
		else $this->history = History::findOrFail($history_id);

		$test = $this->history->test;
		$content = json_decode($test->content);
		$this->area = [
			BlocksArea::SHOW->value => $content->descriptions->show ?? null,
			BlocksArea::CLIENT->value => $content->descriptions->client ?? null,
			BlocksArea::MAIL->value => $content->descriptions->mail ?? null
		];
	}

	/**
	 * Формирование упорядоченной коллекции блоков
	 *
	 * @param BlocksArea $area Название типа описания, которому должна соотвествовать коллекция
	 * @return Collection|null Коллекция блоков
	 */
	public function getBlocks(BlocksArea $area): ?Collection
	{
		$fmptype_id = $this->area[$area->value];
		if (!isset($fmptype_id)) return null;

		$fmptype = FMPType::findOrFail($fmptype_id);
		$this->profile = $fmptype->profiles
			->where('code', $this->history->code)
			->first();

		return $this->profile->blocks
			->sortBy(['sort_no', 'id']);
	}
}
