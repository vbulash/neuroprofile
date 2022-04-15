<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
	use HasFactory, HasTitle;

	protected $fillable = [
		'sort_no',
		'learning',
		'timeout',
		'image1',
		'image2',
		'value1',
		'value2',
		'set_id'
	];

	public function getTitle(): string
	{
		return $this->sort_no;
	}

	public function set()
	{
		return $this->belongsTo(Set::class);
	}
}
