<?php

namespace App\Models;

enum TestOptions: int
{
	// Опции сбора информации от респондента
	case AUTH_GUEST = 0b1;	// Гостевой режим - нет анкеты
	case AUTH_FULL = 0b10;	// Полная анкета (регулируемый состав)
	case AUTH_PKEY = 0b100;	// Только программный ключ
	case AUTH_MIX = 0b1000;	// Комбинированный режим: анкета + программный ключ
	case RESERVED1 = 0b10000;
	// Опции механики
	case IMAGES2 = 0b100000;	// Вопрос из 2 картинок
	case IMAGES4 = 0b1000000;	// Вопрос из 4 картинок (не релизовано)
	// Дополнительные опции механики
	case EYE_TRACKING = 0b10000000;		// Eye-tracking (не реализовано)
	case MOUSE_TRACKING = 0b100000000;	// Mouse-tracking
	case RESERVED2 = 0b1000000000;
	case RESERVED3 = 0b10000000000;
	// Опции показа результата тестирования
	case RESULTS_SHOW = 0b100000000000;		// Показать результат на экране респондента
	case RESULTS_MAIL = 0b1000000000000;	// Отправить результат на почту респонеденту
	case RESULTS_CLIENT = 0b10000000000000;	// Отправить результат на почту клиенту
	case RESERVED4 = 0b100000000000000;
	// Разное
	case CUSTOM_BRANDING = 0b10000000000000000;		// Собственный брендинг
	case CUSTOM_PAYMENT = 0b1000000000000000000;	// Собственная оплата
}