<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $card
 * @property bool $done
 * @property string $code
 * @property bool $paid
 * @method static findOrFail(int $history_id)
 */
class History extends Model implements FormTemplate
{
    use HasFactory;

	protected $table = 'history';

	protected $fillable = [
		'test_id', 'license_id', 'card', 'done', 'code', 'paid'
	];

	public static function uploadLogo(Request $request, string $fileField, string $fileName = null): bool|string|null
	{
		if($request->hasFile($fileField)) {
			if($fileName)
				if(FileLink::unlink($fileName))
					Storage::delete($fileName);
			return $request->file($fileField)->store("images/logo");
		}
		return null;
	}

	public static array $fields = [
		0 => ['title' => 'ID истории', 'sql' => 'history.id', 'code' => 'return $history->id;'],
		1 => ['title' => 'Тестирование завершено', 'sql' => 'history.done', 'code' => 'return $history->done;'],
		2 => ['title' => 'Имя', 'sql' => "history.card->>'$.first_name' AS first_name", 'code' => 'return $history->first_name;'],
		3 => ['title' => 'Фамилия', 'sql' => "history.card->>'$.last_name' AS last_name", 'code' => 'return $history->last_name;'],
		4 => ['title' => 'Электронная почта', 'sql' => "history.card->>'$.email' AS email", 'code' => 'return $history->email;'],
		5 => ['title' => 'Наименование теста', 'sql' => 'tests.name AS tname', 'code' => 'return $history->tname;'],
		6 => ['title' => 'Наименование набора вопросов', 'sql' => 'sets.name AS sname', 'code' => 'return $history->sname;'],
		7 => ['title' => 'Вычисленный код', 'sql' => 'history.code', 'code' => 'return $history->code;'],
		8 => ['title' => 'Номер вопроса', 'sql' => 'questions.sort_no', 'code' => 'return $history->sort_no;'],
		9 => ['title' => 'Учебный вопрос?', 'sql' => 'questions.learning', 'code' => 'return $history->learning == 1 ? "Да" : "Нет";'],
		10 => ['title' => 'Номер выбранного изображения', 'sql' => 'questions.value1, questions.value2',
			'code' => 'return $history->value1 == $history->hskey ? "1" : ($history->value2 == $history->hskey ? "2" : "Ошибка");'],
		11 => ['title' => 'Ключ выбранного изображения', 'sql' => 'historysteps.`key` as hskey', 'code' => 'return $history->hskey;'],
	];

	public function test(): BelongsTo
	{
		return $this->belongsTo(Test::class);
	}

	public function license(): BelongsTo
	{
		return $this->belongsTo(License::class);
	}

	public function steps(): HasMany
	{
		return $this->hasMany(HistoryStep::class);
	}

	public static function createTemplate(): array
	{
		return [];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'history-edit',
			'name' => 'history-edit',
			'action' => route('history.update', ['history' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('history.index', ['sid' => session()->getId()]),
		];
	}

}
