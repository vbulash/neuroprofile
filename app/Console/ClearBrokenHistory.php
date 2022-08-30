<?php

namespace App\Console;

use App\Models\History;
use App\Models\License;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearBrokenHistory
{
	public function __invoke(): void
	{
		$histories = History::whereNull('code');
		$count = $histories->count();
		if ($count > 0) {
			$histories->delete();
			Log::info(sprintf("Удалено поврежденных записей истории прохождения тестов: %d", $count));
		}
	}
}
