<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model implements FormTemplate, Titleable
{
    use HasFactory;

	protected $fillable = [
		'name',
		'code'
	];

	public function getTitle(): string
	{
		return $this->name;
	}

	public function questions()
	{
		return $this->hasMany(Question::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'set-create',
			'name' => 'set-create',
			'action' => route('sets.store', ['sid' => session()->getId()]),
			'close' => route('sets.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'set-edit',
			'name' => 'set-edit',
			'action' => route('sets.update', ['set' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('sets.index', ['sid' => session()->getId()]),
		];
	}
}
