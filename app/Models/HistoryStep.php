<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryStep extends Model
{
    use HasFactory;

	protected $table = 'historysteps';

	protected $fillable = [
		'history_id', 'question_id',
		'key',
		'done'];

	public function history()
	{
		return $this->belongsTo(History::class);
	}

	public function question()
	{
		return $this->belongsTo(Question::class);
	}
}
