<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class History extends Model
{
    use HasFactory;

	protected $table = 'history';

	protected $fillable = [
		'test_id', 'license_id', 'card', 'done', 'code', 'paid'
	];

	public static function uploadLogo(Request $request, string $fileField, string $fileName = null)
	{
		if($request->hasFile($fileField)) {
			if($fileName)
				if(FileLink::unlink($fileName))
					Storage::delete($fileName);
			return $request->file($fileField)->store("images/logo");
		}
		return null;
	}

	public function test()
	{
		return $this->belongsTo(Test::class);
	}

	public function license()
	{
		return $this->belongsTo(License::class);
	}

	public function steps()
	{
		return $this->hasMany(HistoryStep::class);
	}
}
