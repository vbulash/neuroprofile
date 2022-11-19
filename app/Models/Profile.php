<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $code
 * @property string $name
 */
class Profile extends Model implements FormTemplate, Titleable
{
    use HasFactory;

	protected $fillable = [
		'code',
		'name',
		'fmptype_id'
	];

	public function fmptype(): BelongsTo
	{
		return $this->belongsTo(FMPType::class, 'fmptype_id');
	}

	public function blocks(): HasMany
	{
		return $this->hasMany(Block::class);
	}

	public function getTitle(): string
	{
		return sprintf("%s (код %s)", $this->name, $this->code);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'profile-create',
			'name' => 'profile-create',
			'action' => route('profiles.store'),
			'close' => route('profiles.index'),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'profile-edit',
			'name' => 'profile-edit',
			'action' => route('profiles.update', ['profile' => $this->getKey()]),
			'close' => route('profiles.index'),
		];
	}

	public static function getAllCodes(): array
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
