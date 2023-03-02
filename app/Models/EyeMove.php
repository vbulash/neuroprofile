<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EyeMove extends Model
{
    use HasFactory;

	protected $table = 'pupilmoves';

	protected $fillable = [
		'time',	// Время движения
		'X',	// Координата X
		'Y',	// Координата Y
	];

	public function step(): BelongsTo {
		return $this->belongsTo(HistoryStep::class);
	}
}