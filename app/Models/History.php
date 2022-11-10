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
class History extends Model implements FormTemplate {
	use HasFactory;

	protected $table = 'history';

	protected $fillable = [
		'test_id',
		'license_id',
		'card',
		'done',
		'code',
		'paid'
	];

	public static function uploadLogo(Request $request, string $fileField, string $fileName = null): bool|string|null {
		if ($request->hasFile($fileField)) {
			if ($fileName)
				if (FileLink::unlink($fileName))
					Storage::delete($fileName);
			return $request->file($fileField)->store("images/logo");
		}
		return null;
	}

	public static array $groups = [
		[
			'label' => 'О прохождении',
			'fields' => [
				['title' => 'ID истории', 'sql' => 'history.id', 'code' => 'return $history->id;', 'hidden' => true],
				['title' => 'Дата тестирования', 'sql' => 'history.done', 'code' => 'return (new DateTime($history->done))->format("d.m.Y");', 'hidden' => true],
				['title' => 'Время тестирования', 'sql' => 'history.done', 'code' => 'return (new DateTime($history->done))->format("H:i:s");', 'hidden' => true],
				['title' => 'Лицензия', 'sql' => 'licenses.pkey AS pkey', 'code' => 'return $history->pkey;'],
				['title' => 'Вычисленный код', 'sql' => 'history.code', 'code' => 'return $history->code;'],
			]
		],
		[
			'label' => 'О респонденте',
			'fields' => [
				['title' => 'Фамилия', 'sql' => "history.card->>'$.last_name' AS last_name", 'code' => 'return $history->last_name;'],
				['title' => 'Имя', 'sql' => "history.card->>'$.first_name' AS first_name", 'code' => 'return $history->first_name;'],
				['title' => 'Электронная почта', 'sql' => "history.card->>'$.email' AS email", 'code' => 'return $history->email;'],
				['title' => 'Телефон', 'sql' => "history.card->>'$.phone' AS phone", 'code' => 'return $history->phone;'],
				['title' => 'Город', 'sql' => "history.card->>'$.city' AS city", 'code' => 'return $history->city;'],
				[
					'title' => 'Дата рождения',
					'sql' => "history.card->>'$.birth' AS birth",
					'code' => 'return gettype($history->birth) != "NULL" ? (new DateTime($history->birth))->format("d.m.Y") : "";',
					'type' => 'date'
				],
				['title' => 'Возраст', 'sql' => "history.card->>'$.age' AS age", 'code' => 'return $history->age;'],
				['title' => 'Пол', 'sql' => "history.card->>'$.sex' AS sex", 'code' => 'return $history->sex;'],
				['title' => 'Образование (среднее)', 'sql' => "history.card->>'$.education_school' AS education_school", 'code' => 'return $history->education_school;'],
				['title' => 'Образование (среднее профессиональное)', 'sql' => "history.card->>'$.education_middle' AS education_middle", 'code' => 'return $history->education_middle;'],
				['title' => 'Образование (высшее)', 'sql' => "history.card->>'$.education_high' AS education_high", 'code' => 'return $history->education_high;'],
				['title' => 'Место работы', 'sql' => "history.card->>'$.work' AS work", 'code' => 'return $history->work;'],
				['title' => 'Должность', 'sql' => "history.card->>'$.position' AS position", 'code' => 'return $history->position;'],
			]
		],
		[
			'label' => 'О тесте',
			'fields' => [
				['title' => 'Наименование теста', 'sql' => 'tests.name AS tname', 'code' => 'return $history->tname;'],
				['title' => 'Наименование набора вопросов', 'sql' => 'sets.name AS sname', 'code' => 'return $history->sname;'],
				['title' => 'Тип описания для результата на экране', 'sql' => "(SELECT name FROM fmptypes WHERE fmptypes.id = tests.content->>'$.descriptions.show') as fshow", 'code' => 'return $history->fshow;'],
				['title' => 'Тип описания для письма респонденту', 'sql' => "(SELECT name FROM fmptypes WHERE fmptypes.id = tests.content->>'$.descriptions.mail') as fmail", 'code' => 'return $history->fmail;'],
				['title' => 'Тип описания для письма клиенту', 'sql' => "(SELECT name FROM fmptypes WHERE fmptypes.id = tests.content->>'$.descriptions.client') as fclient", 'code' => 'return $history->fclient;'],
			]
		],
		[
			'label' => 'О клиенте',
			'fields' => [
				['title' => 'Номер контракта', 'sql' => 'contracts.number AS coname', 'code' => 'return $history->coname;'],
				['title' => 'Коммерческий контракт?', 'sql' => 'contracts.commercial AS commercial', 'code' => 'return $history->commercial == 1 ? "Да" : "Нет";'],
				['title' => 'Результат оплачен?', 'sql' => 'history.paid', 'code' => 'return $history->paid == 1 ? "Да" : "Нет";'],
				['title' => 'Наименование клиента', 'sql' => 'clients.name AS cname', 'code' => 'return $history->cname;'],
			]
		],
		[
			'label' => 'О вопросах',
			'fields' => [
				['title' => 'Блок ответов на вопросы', 'special' => 'answers'],
			]
		]
	];

	public function test(): BelongsTo {
		return $this->belongsTo(Test::class);
	}

	public function license(): BelongsTo {
		return $this->belongsTo(License::class);
	}

	public function steps(): HasMany {
		return $this->hasMany(HistoryStep::class);
	}

	public static function createTemplate(): array {
		return [];
	}

	public function editTemplate(): array {
		return [
			'id' => 'history-edit',
			'name' => 'history-edit',
			'action' => route('history.update', ['history' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('history.index', ['sid' => session()->getId()]),
		];
	}

	public static function getFields(): array {
		$fields = [];
		foreach (History::$groups as $group)
			foreach ($group['fields'] as $field)
				if (isset($field['special']))
					$fields[] = ['special' => $field['special']];
				else {
					$temp = [
						'title' => $field['title'],
						'code' => $field['code'],
						'sql' => $field['sql'],
					];
					if (isset($field['hidden']))
						$temp['hidden'] = $field['hidden'];
					$fields[] = $temp;
				}
		return $fields;
	}
}
