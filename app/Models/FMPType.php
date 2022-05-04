<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FMPType extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'name',
		'cluster',
		'active',
		'limit'
	];

	public function getTitle(): string
	{
		return $this->title;
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'fmptype-create',
			'name' => 'fmptype-create',
			'action' => route('fmptypes.store', ['sid' => session()->getId()]),
			'close' => route('fmptypes.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'fmptype-edit',
			'name' => 'fmptype-edit',
			'action' => route('fmptypes.update', ['fmptype' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('fmptypes.index', ['sid' => session()->getId()]),
		];
	}
}
