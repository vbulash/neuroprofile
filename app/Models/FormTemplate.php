<?php

namespace App\Models;

interface FormTemplate
{
	public static function createTemplate(): array;
	public function editTemplate(): array;
}
