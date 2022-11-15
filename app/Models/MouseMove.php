<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouseMove extends Model
{
    use HasFactory;

	protected $table = 'mousemoves';

	protected $fillable = [
		'time',	// Время движения
		'X',	// Координата X
		'Y',	// Координата Y
	];

	public function step(): BelongsTo {
		return $this->belongsTo(HistoryStep::class);
	}
}
