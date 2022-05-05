<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Question extends Model implements FormTemplate, Titleable
{
	use HasFactory;

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

	public static array $values = [
		'A+', 'A-',
		'B+', 'B-',
		'C+', 'C-',
		'D+', 'D-',
		'E+', 'E-'
	];

	public function getTitle(): string
	{
		return $this->sort_no;
	}

	public function set()
	{
		return $this->belongsTo(Set::class);
	}

	public static function uploadImage(Request $request, string $imageField, string $image = null)
	{
		if($request->hasFile($imageField)) {
			if($image)
				if(FileLink::unlink($image))
					Storage::delete($image);
			$folder = date('Y-m-d');
			return $request->file($imageField)->store("images/{$folder}");
		}
		return null;
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'question-create',
			'name' => 'question-create',
			'action' => route('questions.store', ['sid' => session()->getId()]),
			'close' => route('questions.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'question-edit',
			'name' => 'question-edit',
			'action' => route('questions.update', ['question' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('questions.index', ['sid' => session()->getId()]),
		];
	}
}
