<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'iname',
		'itype',
		'status',
		'program',
		'employer_id'
	];

	public function getTitle(): string
	{
		return $this->iname;
	}

	public function employer() {
		return $this->belongsTo(Employer::class);
	}

	public function timetables() {
		return $this->hasMany(Timetable::class);
	}
}
