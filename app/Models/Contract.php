<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
	use HasFactory, HasTitle;

	public const ACTIVE = 'Активный';
	public const INACTIVE = 'Неактивный';
	public const COMPLETE_BY_DATE = 'Истёк';
	public const COMPLETE_BY_COUNT = 'Закончились лицензии';

	protected $fillable = [
		'number',
		'invoice',
		'start',
		'end',
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

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function licenses()
	{
		return $this->hasMany(License::class);
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
}
