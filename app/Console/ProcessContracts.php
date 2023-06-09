<?php


namespace App\Console;


use App\Models\Contract;
use App\Models\License;
use DateTime;
use Illuminate\Support\Facades\Log;

class ProcessContracts {
	public function __invoke() {
		$contracts = Contract::all();
		foreach ($contracts as $contract) {
			$current = $contract->status;

			$today = new DateTime();
			$start = $contract->start;
			$end = $contract->end;

			$status = Contract::INACTIVE;
			if (($today >= $start) && ($today < $end)) {
				$status = Contract::ACTIVE;

				$count = $contract->licenses()->where('status', '=', License::FREE)->count();
				if ($count <= 0)
					$status = Contract::COMPLETE_BY_COUNT;
			}
			if ($today > $end)
				$status = Contract::COMPLETE_BY_DATE;

			if ($status != $current) {
				$contract->status = $status;
				$contract->update();

				$statuses = [
					Contract::INACTIVE => 'Неактивен (дата начала в будущем)',
					Contract::ACTIVE => 'Исполняется',
					Contract::COMPLETE_BY_DATE => 'Завершен по дате',
					Contract::COMPLETE_BY_COUNT => 'Закончились свободные лицензии'
				];

				Log::info(sprintf("Статус контракта № %s (ID %d, клиент '%s') изменен с '%s' на '%s'",
					$contract->number, $contract->getKey(), $contract->client->getTitle(),
					$statuses[$current], $statuses[$status]));
			}
		}
	}

}