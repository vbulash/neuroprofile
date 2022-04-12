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
		return $this->name;
	}

	// Геттеры Laravel
	protected function start(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				switch (env('DB_CONNECTION')) {
					case 'sqlite':
						return $value;
					case 'mysql':
					default:
						$date = DateTime::createFromFormat('Y-m-d', $value);
						return $date->format('d.m.Y');
				}
			});
	}

	protected function end(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				switch (env('DB_CONNECTION')) {
					case 'sqlite':
						return $value;
					case 'mysql':
					default:
						$date = DateTime::createFromFormat('Y-m-d', $value);
						return $date->format('d.m.Y');
				}
			});
	}

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
