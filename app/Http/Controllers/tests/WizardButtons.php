<?php

namespace App\Http\Controllers\tests;

enum WizardButtons: int
{
	case BACK = 0b1;
	case NEXT = 0b10;
	case FINISH = 0b100;
}
