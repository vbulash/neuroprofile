<?php

namespace App\Http\Controllers\results;

use App\Models\History;
use App\Models\Test;

class CardComposer
{
	private History $history;

	/**
	 * @param int|History $history_id ID или объект записи истории
	 */
	public function __construct(int|History $history_id)
	{
		if ($history_id instanceof History) $this->history = $history_id;
		else $this->history = History::findOrFail($history_id);
	}

	public function getCard(bool $extra = false): ?array
	{
		$history_card = $this->history->card ? json_decode($this->history->card) : null;

		if (!isset($history_card)) return null;

		$card = [];
		foreach (Test::$fields as $group) foreach ($group as $field) {
			$name = $field['name'];
			$title = $field['label'];
			if (isset($history_card->$name))
				if ($extra) {
					$items = [
						'name' => $name,
						'title' => $title,
						'value' => $history_card->$name,
						'required' => $field['required'],
						'type' => $field['type']
					];
					if ($field['type'] == 'select') {
						$options = [];
						foreach ($field['cases'] as $case)
							$options[$case['value']] = $case['label'];
						$items['options'] = $options;
					}
					$card[] = $items;
				}
			else
				$card[$title] = $history_card->$name;
		}
		return $card;
	}
}
