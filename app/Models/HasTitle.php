<?php

namespace App\Models;

trait HasTitle
{
	public abstract function getTitle(): string;
}
