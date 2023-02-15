<?php

namespace Database\Seeders;

use App\Models\Set;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExhibitionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$source = Set::find(1);
		$target = $source->replicate()->fill([
			'name' => 'Артигас-выставка'
		]);
		$target->save();
		foreach ($source->questions as $question) {
			if ($question->learning)
				continue;

			$targetQuestion = $question->replicate()->fill([
				'cue' => null,
				'created_at' => Carbon::now()
			]);
			$targetQuestion->set()->associate($target);
			$targetQuestion->save();

			foreach ($question->parts as $part) {
				$targetPart = $part->replicate()->fill([
					'created_at' => Carbon::now()
				]);
				$targetPart->question()->associate($targetQuestion);
				$targetPart->save();
			}
		}
	}
}