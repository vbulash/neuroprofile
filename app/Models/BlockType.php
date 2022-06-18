<?php

namespace App\Models;

enum BlockType: int
{
	case Text = 1;
	case Alias = 2;
	case Image = 3;

	public static function getName(int $bt): string
	{
		return match($bt) {
			BlockType::Text->value => 'Текстовый блок',
			BlockType::Alias->value => 'Ссылочный блок',
			BlockType::Image->value => 'Блок-изображение',
			default => 'Неизвестный тип блока'
		};
	}
}
