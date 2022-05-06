<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\BlockType;
use App\Models\FMPType;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TestDescriptionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$fmptypes = json_decode(Storage::get("migrations/fmptypes.json"));
		$fmptypes_map = [];
		foreach ($fmptypes as $fmptype_json) {
			$fmptype = FMPType::create([
				'name' => $fmptype_json->name,
				'cluster' => $fmptype_json->cluster,
				'active' => $fmptype_json->active,
				'limit' => $fmptype_json->limit
			]);
			$fmptypes_map[$fmptype_json->id] = $fmptype->getKey();
		}

		$profiles = json_decode(Storage::get("migrations/neuroprofiles.json"));
		$profiles_map = [];
		foreach ($profiles as $profile_json) {
			$profile = Profile::create([
				'code' => $profile_json->code,
				'name' => $profile_json->name,
				'fmptype_id' => $fmptypes_map[$profile_json->fmptype_id],
			]);
			$profiles_map[$profile_json->id] = $profile->getKey();
		}

		$blocks = json_decode(Storage::get("migrations/blocks.json"));
		foreach ($blocks as $block_json) {
			$block = Block::create([
				'sort_no' => $block_json->sort_no,
				'name' => $block_json->description,
				'type' => BlockType::Text->value,
				'full' => $block_json->content,
				'short' => $block_json->free,
				'profile_id' => $profiles_map[$block_json->neuroprofile_id],
			]);
		}
	}
}
