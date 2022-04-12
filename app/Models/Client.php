<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, HasTitle;

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
}
