<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kind extends Model implements FormTemplate, Titleable {
	use HasFactory;

	protected $fillable = [
		'name',		// Название типа вопроса
		'images',	// Количество изображений в вопросе
		'answers',	// Количество ответов в вопросе
		'keys',		// Ключи вопросов
		'cue',		// Подсказка к вопросам
	];

	public function questions(): HasMany {
		return $this->hasMany(Question::class);
	}

	function getTitle(): string {
		return $this->name;
	}

	public static function createTemplate(): array {
		return [
			'id' => 'kind-create',
			'name' => 'kind-create',
			'action' => route('kinds.store'),
			'close' => route('kinds.index'),
		];
	}

	public function editTemplate(): array {
		return [
			'id' => 'kind-edit',
			'name' => 'kind-edit',
			'action' => route('kinds.update', ['kind' => $this->getKey()]),
			'close' => route('kinds.index'),
		];
	}
}
