<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 */
class Block extends Model implements FormTemplate, Titleable
{
    use HasFactory, UploadImage;

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
			'action' => route('blocks.store'),
			'close' => route('blocks.index'),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'block-edit',
			'name' => 'block-edit',
			'action' => route('blocks.update', ['block' => $this->getKey()]),
			'close' => route('blocks.index'),
		];
	}

	public function parent(): BelongsTo
	{
		return $this->belongsTo(Block::class, 'block_id');
	}

	public function children(): HasMany
	{
		return $this->hasMany(Block::class);
	}

	public function profile(): BelongsTo
	{
		return $this->belongsTo(Profile::class);
	}
}
