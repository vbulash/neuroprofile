<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ReportDataController as RDC;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class MainController extends Controller
{
	private function testAllLetter(int $count): string {
		$letter = ' ';
		if(($count < 10) || ($count > 20)) {
			$letter .= match ($count % 10) {
				1 => 'тест пройден',
				2, 3, 4 => 'теста пройдено',
				default => 'тестов пройдено',
			};
		} else $letter .= 'тестов пройдено';

		return $letter;
	}

	private function testPaidLetter(int $count): string {
		$letter = ' ';
		if(($count < 10) || ($count > 20)) {
			$letter .= match ($count % 10) {
				1 => 'тест оплачен',
				2, 3, 4 => 'теста оплачено',
				default => 'тестов оплачено',
			};
		} else $letter .= 'тестов оплачено';

		return $letter;
	}

	public function index(): Factory|View|Application
	{
		RDC::init();

		$data = [
			RDC::HISTORY_ALL_COUNT => RDC::get(RDC::HISTORY_ALL_COUNT),
			RDC::HISTORY_PAID_COUNT => RDC::get(RDC::HISTORY_PAID_COUNT),
			RDC::HISTORY_DYNAMIC_LABELS => RDC::get(RDC::HISTORY_DYNAMIC_LABELS),
			RDC::HISTORY_DYNAMIC_ALL_COUNT => RDC::get(RDC::HISTORY_DYNAMIC_ALL_COUNT),
			RDC::HISTORY_DYNAMIC_PAID_COUNT => RDC::get(RDC::HISTORY_DYNAMIC_PAID_COUNT),
		];
		$data[RDC::HISTORY_PAID_COUNT . '.letter'] = $this->testPaidLetter($data[RDC::HISTORY_PAID_COUNT]);
		$data[RDC::HISTORY_ALL_COUNT . '.letter'] = $this->testAllLetter($data[RDC::HISTORY_ALL_COUNT]);

		return view('main', compact('data'));
	}
}
