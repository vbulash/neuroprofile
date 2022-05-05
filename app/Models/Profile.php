<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model implements FormTemplate, Titleable
{
    use HasFactory;

	protected $fillable = [
		'code',
		'name',
		'fmptype_id'
	];

	public function fmptype() {
		return $this->belongsTo(FMPType::class);
	}

	public function getTitle(): string
	{
		return $this->name;
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'profile-create',
			'name' => 'profile-create',
			'action' => route('profiles.store', ['sid' => session()->getId()]),
			'close' => route('profiles.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'fmptype-edit',
			'name' => 'fmptype-edit',
			'action' => route('profiles.update', ['profile' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('profiles.index', ['sid' => session()->getId()]),
		];
	}

	private static function getAllCodes(): array
	{
		return [
			'BD' => 'BD',
			'BH' => 'BH',
			'BO' => 'BO',
			'BP' => 'BP',
			'CI' => 'CI',
			'CO' => 'CO',
			'CS' => 'CS',
			'CV' => 'CV',
			'OA' => 'OA',
			'OI' => 'OI',
			'OO' => 'OO',
			'OV' => 'OV',
			'PA' => 'PA',
			'PK' => 'PK',
			'PP' => 'PP',
			'PR' => 'PR',
		];
	}

	public static function getFreeCodes(int $id): array
	{
		$fmptype = FMPType::findOrFail($id);
		$all = collect(self::getAllCodes());
		$existing = $fmptype->profiles
			->pluck('code', 'code');	// Интересно - так можно?

		$result = $all->diff($existing)->toArray();
		return $result;
	}
}
