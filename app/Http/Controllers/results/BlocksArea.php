<?php

namespace App\Http\Controllers\results;

enum BlocksArea: string
{
	case SHOW = 'show';		// Блоки для отображения на экране
	case MAIL = 'mail';		// Блоки для письма респонденту
	case CLIENT = 'client';	// Блоки для письма клиенту
}
