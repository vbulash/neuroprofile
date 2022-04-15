<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'name',
		'code'
	];

	public function getTitle(): string
	{
		return $this->name;
	}

	public function questions()
	{
		return $this->hasMany(Question::class);
	}
}
