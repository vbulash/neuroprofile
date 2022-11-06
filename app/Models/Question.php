<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model implements FormTemplate, Titleable {
	use HasFactory;

	protected $fillable = [
		'sort_no',	// Номер по порядку
		'learning',	// Учебный вопрос
		'timeout',	// Таймаут вопроса (0 - нет таймаута)
		'cue',		// Подсказка к вопросу
	];

	public function getTitle(): string {
		return sprintf("%d (%s)", $this->sort_no, $this->kind->getTitle());
	}

	public function set(): BelongsTo {
		return $this->belongsTo(Set::class);
	}

	public function parts(): HasMany {
		return $this->hasMany(Part::class);
	}

	public function kind(): BelongsTo {
		return $this->belongsTo(Kind::class);
	}

	public static function createTemplate(): array {
		return [
			'id' => 'question-create',
			'name' => 'question-create',
			'action' => route('questions.store', ['sid' => session()->getId()]),
			'close' => route('questions.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array {
		return [
			'id' => 'question-edit',
			'name' => 'question-edit',
			'action' => route('questions.update', ['question' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('questions.index', ['sid' => session()->getId()]),
		];
	}
}
