<?php

namespace App\Models;

enum ContractStatus: int
{
	case NEW = 0;
	case ACTIVE = 1;
	case EXPIRED = 2;
	case NO_LICENSES = 3;
	case PAUSED = 4;

	public static function getName(int $cs): string
	{
		return match ($cs) {
			self::NEW->value => 'Новый',
			self::ACTIVE->value => 'Активный',
			self::EXPIRED->value => 'Завершился',
			self::NO_LICENSES->value => 'Закончились лицензии',
			self::PAUSED->value => 'Приостановлен'
		};
	}
}
