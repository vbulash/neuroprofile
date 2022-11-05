<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\Question;
use Illuminate\Database\Seeder;

class PartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		foreach (Question::all() as $question) {
			$part = new Part();
			$part->image = $question->image1;
			$part->key = $question->value1;
			$question->parts()->save($part);
			$part->save();
			$part = new Part();
			$part->image = $question->image2;
			$part->key = $question->value2;
			$question->parts()->save($part);
			$part->save();
		}
	}
}
