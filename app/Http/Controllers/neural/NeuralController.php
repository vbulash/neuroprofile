<?php

namespace App\Http\Controllers\neural;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessFaceShot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class NeuralController extends Controller {
	public function run(Request $request) {

	}
	private static array $imagetypes = [
	IMAGETYPE_GIF => 'GIF',
	IMAGETYPE_JPEG => 'JPEG',
	IMAGETYPE_PNG => 'PNG',
	IMAGETYPE_SWF => 'SWF',
	IMAGETYPE_PSD => 'PSD',
	IMAGETYPE_BMP => 'BMP',
	IMAGETYPE_TIFF_II => 'TIFF II',
	IMAGETYPE_TIFF_MM => 'TIFF_MM',
	IMAGETYPE_JPC => 'JPC',
	IMAGETYPE_JP2 => 'JP2',
	IMAGETYPE_JPX => 'JPX',
	IMAGETYPE_JB2 => 'JB2',
	IMAGETYPE_SWC => 'SWC',
	IMAGETYPE_IFF => 'IFF',
	IMAGETYPE_WBMP => 'WBMP',
	IMAGETYPE_XBM => 'XBM',
	IMAGETYPE_ICO => 'ICO',
	IMAGETYPE_WEBP => 'WEBP',
	];

	private function getClearBase64(string $fullBase64): string {
		$image_parts = explode(";base64,", $fullBase64);
		return $image_parts[1];
	}
	/**
	 * Приёмка фотографии от модуля съёмки
	 *
	 */
	public function shotDone(Request $request) {
		// Декодировать запрос
		$uuid = $request->uuid;
		// Сохранить временную картинку .png для анализа формата
		$tmpimage = 'tmp/' . Str::uuid() . '.png';
		$clearPhoto = $this->getClearBase64($request->photo);
		$image_base64 = base64_decode($clearPhoto);
		Storage::put($tmpimage, $image_base64);

		// Анализ формата временной картинки
		$status = 200;
		$content = '';
		try {
			$type = exif_imagetype(Storage::path($tmpimage));

			if ($type == false) {
				$status = 204;
				$content = 'Не файл изображения';
			} elseif ($type == IMAGETYPE_PNG)
				;
			elseif (isset(self::$imagetypes[$type])) {
				$status = 204;
				$content = sprintf("Изображение формата %s, ожидается PNG", self::$imagetypes[$type]);
			} else {
				$status = 500;
				$content = 'Неизвестная ошибка платформы';
			}
		} catch (Throwable $exc) {
			$status = 204;
			$content = 'Ошибка: ' . $exc->getMessage();
		}

		// Сохранение целевого фото
		if ($status == 200) {
			$destfile = sprintf("neural/%s/%s_1.png", $uuid, $uuid);
			Storage::move($tmpimage, $destfile);
		}
		Storage::delete($tmpimage);

		if ($status != 200) {
			Log::error($content);
			return response()->json([
				'error' => $content
			], $status);
		}

		return '';

		// Параллельное выполнение цикла тестирования респондентом и работы нейросети
		$content = (object) [
			'uuid' => $uuid,
			'photo' => $clearPhoto,
			'sex' => 'M',
		];
		ProcessFaceShot::dispatch($content);
	}
}
