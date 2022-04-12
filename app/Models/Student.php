<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
	use HasFactory, HasTitle;

	protected $fillable = [
		'lastname',
		'firstname',
		'surname',
		'sex',
		'birthdate',
		'phone',
		'email',
		'parents',
		'parentscontact',
		'passport',
		'address',
		'institutions',
		'grade',
		'hobby',
		'hobbyyears',
		'contestachievements',
		'dream',
		'documents',
		'user_id'
	];
	public function getTitle(): string
	{
		return sprintf("%s %s%s",
			$this->lastname, $this->firstname, $this->surname ? ' ' . $this->surname : '');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function histories() {
		return $this->hasMany(History::class);
	}
}
