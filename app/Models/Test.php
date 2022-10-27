<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 * @property int $options
 * @property string $content
 * @property string $key
 * @property int $paid
 * @method static findOrFail(int $int)
 */
class Test extends Model implements Titleable
{
    use HasFactory, TestFields, UploadImage;

	protected $fillable = [
		'name',			// Название теста
		'options',		// Опции теста
		'content',		// JSON со сложными параметрами теста
		'key',			// Ключ теста
		'paid',			// Отметка платности теста
		'contract_id',	// Связанный контракт
		'set_id',		// Связанный набор вопросов
		'cue',			// Подсказка к вопросу
	];

	/**
	 * Контракт теста
	 * @return BelongsTo
	 */
	public function contract()
	{
		return $this->belongsTo(Contract::class);
	}

	/**
	 * Набор вопросов теста
	 * @return BelongsTo
	 */
	public function set()
	{
		return $this->belongsTo(Set::class);
	}

	public function getTitle(): string
	{
		return $this->name;
	}

	// Генератор Key
	public static function generateKey()
	{
		return uniqid('test_', true);
	}
}
