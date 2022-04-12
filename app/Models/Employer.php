<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'name',
		'contact',
		'address',
		'phone',
		'email',
		'inn',
		'kpp',
		'ogrn',
		'official_address',
		'post_address',
		'description',
		'expectation',
		'nda',
		'user_id'
	];

	public function getTitle(): string
	{
		return $this->name;
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function internships()
	{
		return $this->hasMany(Internship::class);
	}
}
