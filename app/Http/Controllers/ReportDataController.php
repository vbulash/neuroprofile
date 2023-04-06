<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\HistStat;

class ReportDataController extends Controller {
	// События модели
	public const HISTORY_ALL_COUNT = 'history.all.count';
	public const HISTORY_PAID_COUNT = 'history.paid.count';
	public const HISTORY_DYNAMIC = 'history.dynamic';
	public const HISTORY_DYNAMIC_LABELS = 'history.dynamic.labels';
	public const HISTORY_DYNAMIC_ALL_COUNT = 'history.dynamic.all.count';
	public const HISTORY_DYNAMIC_PAID_COUNT = 'history.dynamic.paid.count';

	protected static array $reportData = [];

	/**
	 * @param string $key
	 */
	public static function generate(string $key): void {
		switch ($key) {
			case self::HISTORY_ALL_COUNT:
				$count = History::all()->count();
				self::$reportData[$key] = $count;
				break;
			case self::HISTORY_PAID_COUNT:
				$count = History::all()->where('paid', true)->count();
				self::$reportData[$key] = $count;
				break;
			case self::HISTORY_DYNAMIC:
				$histstat = HistStat::limit(14)->get()->reverse();
				self::$reportData[self::HISTORY_DYNAMIC_LABELS] = [];
				self::$reportData[self::HISTORY_DYNAMIC_LABELS] = $histstat->pluck('day')->toArray();
				self::$reportData[self::HISTORY_DYNAMIC_PAID_COUNT] = $histstat->pluck('paid')->toArray();
				self::$reportData[self::HISTORY_DYNAMIC_ALL_COUNT] = $histstat->pluck('total')->toArray();
				break;
		}
	}

	public static function get(string $key) {
		return self::$reportData[$key];
	}

	/**
	 * Генерация всего пула данных для отчетов
	 */
	public static function init(): void {
		self::generate(self::HISTORY_ALL_COUNT);
		self::generate(self::HISTORY_PAID_COUNT);
		self::generate(self::HISTORY_DYNAMIC);
	}
}