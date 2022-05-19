<?php

namespace App\Models;

enum BlockType: int
{
	case Text = 1;
	case Alias = 2;

	public static function getName(int $bt): string
	{
		return match($bt) {
			BlockType::Text->value => 'Текстовый блок',
			BlockType::Alias->value => 'Ссылочный блок',
			default => 'Неизвестный тип блока'
		};
	}
}
