<?php

namespace App\Models;

enum QuestionKind: int {
	case SINGLE2 = 0;
	case MULTIPLE = 10;

	public static function getName(int $qk) {
		return match ($qk) {
			self::SINGLE2->value => 'Один ответ, 2 изображения',
			self::MULTIPLE->value => 'Множественные ответы, много изображений',
			default => 'Тип вопроса не определен'
		};
	}
}
