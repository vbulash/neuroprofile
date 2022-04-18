<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLink extends Model
{
    use HasFactory;

	protected $table = 'filelinks';
	protected $fillable = [
		'filename',
		'linkcount'
	];

	/**
	 * Учет добавления ресурса в хранилище
	 * @param string $mediaPath Путь файла для учета ссылки
	 */
	public static function link(string $mediaPath) {
		$fileLinks = self::all()->where('filename', $mediaPath);
		if ($fileLinks->count() == 0) {
			$fileLink = self::create([
				'filename' => $mediaPath,
				'linkcount' => 1
			]);
			$fileLink->save();
		} else {
			$fileLink = $fileLinks->first();
			$fileLink->linkcount = $fileLink->linkcount + 1;
			$fileLink->update();
		}
	}

	public static function unlink(string $mediaPath): bool {
		$fileLinks = self::all()->where('filename', $mediaPath);
		if ($fileLinks->count() == 0) {
			return false;   // Нечего удалять
		} else {
			$fileLink = $fileLinks->first();
			$fileLink->linkcount = $fileLink->linkcount - 1;
			$fileLink->update();
			return ($fileLink->linkcount == 0); // Можно удалять, если ссылок больше нет
		}
	}
}
