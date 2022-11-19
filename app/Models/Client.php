<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail(mixed $client)
 */
class Client extends Model implements FormTemplate, Titleable
{
    use HasFactory;

	protected $fillable = [
		'name',
		'inn',
		'ogrn',
		'address',
		'phone',
		'email'
	];

	public function getTitle(): string
	{
		return $this->name;
	}

	public function contracts() {
		return $this->hasMany(Contract::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'client-create',
			'name' => 'client-create',
			'action' => route('clients.store'),
			'close' => route('clients.index'),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'client-edit',
			'name' => 'client-edit',
			'action' => route('clients.update', ['client' => $this->getKey()]),
			'close' => route('clients.index'),
		];
	}
}
