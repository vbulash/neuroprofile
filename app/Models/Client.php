<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
			'action' => route('clients.store', ['sid' => session()->getId()]),
			'close' => route('clients.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'client-edit',
			'name' => 'client-edit',
			'action' => route('clients.update', ['client' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('clients.index', ['sid' => session()->getId()]),
		];
	}
}
