<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Part extends Model implements FormTemplate {
	use HasFactory, UploadImage;

	protected $fillable = [
		'image',	// Изображение вопроса
		'key',		// Ключ изображения

	];

	public function question(): BelongsTo {
		return $this->belongsTo(Question::class);
	}

	public static function createTemplate(): array {
		return [
			'id' => 'part-create',
			'name' => 'part-create',
			'action' => route('parts.store'),
			'close' => route('parts.index'),
		];
	}

	public function editTemplate(): array {
		return [
			'id' => 'part-edit',
			'name' => 'part-edit',
			'action' => route('parts.update', ['part' => $this->getKey()]),
			'close' => route('parts.index'),
		];
	}
}
