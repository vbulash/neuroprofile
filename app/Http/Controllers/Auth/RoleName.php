<?php

namespace App\Http\Controllers\Auth;

enum RoleName: string {
	case ADMIN = 'Администратор платформы';
	case CLIENT_ADMIN = 'Аккаунт менеджер';
}