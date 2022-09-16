<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $card
 * @property bool $done
 * @property string $code
 * @property bool $paid
 * @method static findOrFail(int $history_id)
 */
class History extends Model implements FormTemplate
{
    use HasFactory;

	protected $table = 'history';

	protected $fillable = [
		'test_id', 'license_id', 'card', 'done', 'code', 'paid'
	];

	public static function uploadLogo(Request $request, string $fileField, string $fileName = null): bool|string|null
	{
		if($request->hasFile($fileField)) {
			if($fileName)
				if(FileLink::unlink($fileName))
					Storage::delete($fileName);
			return $request->file($fileField)->store("images/logo");
		}
		return null;
	}

	public function test(): BelongsTo
	{
		return $this->belongsTo(Test::class);
	}

	public function license(): BelongsTo
	{
		return $this->belongsTo(License::class);
	}

	public function steps(): HasMany
	{
		return $this->hasMany(HistoryStep::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'history-create',
			'name' => 'history-create',
			'action' => route('history.store', ['sid' => session()->getId()]),
			'close' => route('history.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'history-edit',
			'name' => 'history-edit',
			'action' => route('history.update', ['history' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('history.index', ['sid' => session()->getId()]),
		];
	}

}