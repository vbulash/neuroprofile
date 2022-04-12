<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class History extends Model
{
    use HasFactory, Notifiable;

	protected $fillable = [
		'timetable_id',
		'student_id',
		'status'
	];

	public function timetable() {
		return $this->belongsTo(Timetable::class);
	}

	public function student() {
		return $this->belongsTo(Student::class);
	}
}
