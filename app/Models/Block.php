<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model implements FormTemplate, Titleable
{
    use HasFactory;

	protected $fillable = [
		'sort_no',
		'name',
		'type',
		'full',
		'short',
		'block_id',
		'profile_id'
	];

	public function getTitle(): string
	{
		return $this->name;
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'block-create',
			'name' => 'block-create',
			'action' => route('blocks.store', ['sid' => session()->getId()]),
			'close' => route('blocks.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'block-edit',
			'name' => 'block-edit',
			'action' => route('blocks.update', ['block' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('blocks.index', ['sid' => session()->getId()]),
		];
	}

	public function parent()
	{
		return $this->belongsTo(Block::class, 'block_id');
	}

	public function children()
	{
		return $this->hasMany(Block::class);
	}

	public function profile()
	{
		return $this->belongsTo(Profile::class);
	}
}
