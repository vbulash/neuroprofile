<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait UploadImage
{
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
}
