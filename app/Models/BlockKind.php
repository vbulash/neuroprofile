<?php

namespace App\Models;

enum BlockKind: string
{
	case Block = 'block';
	case Parent = 'parent';
	case Kid = 'kid';

	public static function getName(string $bk): string
	{
		return match($bk) {
			self::Block->value => 'Стандартный блок',
			self::Parent->value => 'Блок-предок',
			self::Kid->value => 'Блок-потомок',
			default => 'Неизвестный вид блока'
		};
	}
}
