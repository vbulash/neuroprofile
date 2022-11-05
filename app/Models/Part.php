<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Part extends Model
{
    use HasFactory;

	protected $fillable = [
		'image',	// Изображение вопроса
		'key',		// Ключ изображения
	];

	public function question(): BelongsTo {
		return $this->belongsTo(Question::class);
	}
}
