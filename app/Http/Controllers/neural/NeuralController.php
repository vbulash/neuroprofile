<?php

namespace App\Http\Controllers\neural;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

use function PHPSTORM_META\type;

class NeuralController extends Controller {
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

	public function shotDone(Request $request) {
		// Декодировать запрос
		$png = base64_decode($request->photo);
		// Сохранить временную картинку .png для анализа формата
		$tmpimage = 'tmp/' . Str::uuid() . '.png';
		Storage::put($tmpimage, $png);
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
			$destfile = 'neural/faces/face1.png'; // TODO Сгенерировать реальные папку и имя изображения
			Storage::move($tmpimage, $destfile);
		}
		Storage::delete($tmpimage);

		return $status == 200 ?
			response('') :
			response()->json([
				'error' => $content
			], $status);
	}

	public function netDone(Request $request) {
		return response('');
	}
}
