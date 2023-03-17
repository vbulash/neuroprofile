<?php

namespace App\Models;

trait TestFields {
	public static array $fields = [
		[
			[
				"actual" => true,
				"required" => true,
				"name" => "first_name",
				"label" => "Имя",
				"type" => "text",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => false,
				"name" => "surname",
				"label" => "Отчество",
				"type" => "text",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => true,
				"name" => "last_name",
				"label" => "Фамилия",
				"type" => "text",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => true,
				"name" => "email",
				"label" => "Электронная почта",
				"type" => "email",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => false,
				"name" => "phone",
				"label" => "Телефон",
				"type" => "phone",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => true,
				"name" => "city",
				"label" => "Город",
				"type" => "text",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => false,
				"name" => "birth",
				"label" => "Дата рождения",
				"type" => "date",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => true,
				"name" => "age",
				"label" => "Возраст",
				"type" => "number",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => true,
				"name" => "sex",
				"label" => "Пол",
				"type" => "select",
				"value" => "F",
				"cases" => [
					["value" => "M", "label" => "Мужской"],
					["value" => "F", "label" => "Женский"]
				]
			]
		],
		[
			[
				"actual" => true,
				"required" => false,
				"name" => "education_school",
				"label" => "Образование (среднее)",
				"type" => "text",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => false,
				"name" => "education_middle",
				"label" => "Образование (среднее профессиональное)",
				"type" => "text",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => false,
				"name" => "education_high",
				"label" => "Образование (высшее)",
				"type" => "text",
				"value" => ""
			],
		],
		[
			[
				"actual" => true,
				"required" => false,
				"name" => "work",
				"label" => "Место работы",
				"type" => "text",
				"value" => ""
			],
			[
				"actual" => true,
				"required" => false,
				"name" => "position",
				"label" => "Должность",
				"type" => "text",
				"value" => ""
			]
		]
	];
}