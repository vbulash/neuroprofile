<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $number
 * @property bool commercial
 * @method static findOrFail(mixed $contract)
 * @method static create(array $data)
 * @method static find(int $id)
 */
class Contract extends Model implements FormTemplate, Titleable
{
	use HasFactory;

	public const ACTIVE = 'Активный';
	public const INACTIVE = 'Неактивный';
	public const COMPLETE_BY_DATE = 'Истёк';
	public const COMPLETE_BY_COUNT = 'Закончились лицензии';

	protected $fillable = [
		'number',
		'invoice',
		'start',
		'end',
		'commercial',
		'mkey',
		'license_count',
		'url',
		'status',
		'client_id'
	];

	public function getTitle(): string
	{
		return '№ ' . $this->number;
	}

	// Геттеры Laravel
	private static function convert2Date($value): DateTime
	{
		if($value instanceof DateTime)
			return $value;
		else {
			$temp = new DateTime($value);
			return $temp;
		}
	}

	protected function start(): Attribute
	{
		return Attribute::make(
			get: fn($value) => self::convert2Date($value),
			set: fn($value) => self::convert2Date($value),
		);

	}

	protected function end(): Attribute
	{
		return Attribute::make(
			get: fn($value) => self::convert2Date($value),
			set: fn($value) => self::convert2Date($value),
		);
	}

	public function client(): BelongsTo
	{
		return $this->belongsTo(Client::class);
	}

	public function licenses(): HasMany
	{
		return $this->hasMany(License::class);
	}

	public function tests(): HasMany
	{
		return $this->hasMany(Test::class);
	}

	// Генератор MKey
	public static function generateKey(string $url): string
	{
		$first = uniqid('mkey_', true);
		$last = sprintf("%u", crc32($url));
		return $first . '*' . $last;
	}

	public static function checkUrl(string $mkey, string $url): bool
	{
		$parts = explode('*', $mkey);
		$crc = sprintf("%u", crc32($url));
		return ($parts[1] == $crc);
	}

	public function updateStatus(): void {
		$today = new DateTime();
		$this->status = Contract::INACTIVE;
		if (($today >= $this->start) && ($today < $this->end)) $this->status = Contract::ACTIVE;
		if ($today > $this->end) $this->status = Contract::COMPLETE_BY_DATE;
		$this->update();
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'contract-create',
			'name' => 'contract-create',
			'action' => route('contracts.store', ['sid' => session()->getId()]),
			'close' => route('contracts.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'contract-edit',
			'name' => 'contract-edit',
			'action' => route('contracts.update', ['contract' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('contracts.index', ['sid' => session()->getId()]),
		];
	}
}
