<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistStat extends Model
{
    use HasFactory;

	protected $table = 'histstat';
	protected $fillable = ['day', 'total', 'paid'];
}
