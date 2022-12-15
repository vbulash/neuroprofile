<?php

namespace App\Http\Controllers\neural;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

	/**
	 * Приёмка фотографии от модуля съёмки
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
	 */
	public function shotDone(Request $request) {
		// Декодировать запрос
		$png = base64_decode($request->photo);
		$uuid = $request->uuid;
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
			$destfile = sprintf("neural/%s/%s_1.png", $uuid, $uuid);
			Storage::move($tmpimage, $destfile);
		}
		Storage::delete($tmpimage);

		return $status == 200 ?
			response('') :
			response()->json([
				'error' => $content
			], $status);
	}

	/**
	 * Инициирование нейросети
	 * @param Request $request
	 * @return void
	 */
	public function netUp() {
		//$uuid = session('pkey');
		$uuid = 'pkey_628c8b6d245934.76213557';
		$source = sprintf("neural/%s/%s_1.png", $uuid, $uuid);
		$photo = Storage::get($source);
		$sex = 'M';
	}

	/**
	 * Приёмка результата работы нейросети
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public function netDone(Request $request) {
		// Декодировать запрос
		$result = $request->result;
		$uuid = $request->uuid;
		$codeMap = [
			'A' => 'BD',
			'B' => 'BH',
			'C' => 'BO',
			'D' => 'BP',
			'F' => 'CI',
			'G' => 'CO',
			'H' => 'CS',
			'K' => 'CV',
			'L' => 'OA',
			'M' => 'OI',
			'N' => 'OO',
			'O' => 'OV',
			'P' => 'PA',
			'Q' => 'PK',
			'R' => 'PP',
			'S' => 'PR',
		];

		$neural = [];
		foreach ($result as $item)
			$neural[] = [
				'code' => $codeMap[$item['code']],
				'average' => $item['average'],
				'meansquare' => $item['meansquare'],
			];

		$license = License::where('pkey', $uuid)->first();
		$history = $license->history;
		if (!isset($history))
			return response('Поврежден UUID', 204);

		$card = json_decode($history->card, true);
		$card['neural'] = $neural;
		$history->update([
			'card' => $card
		]);

		return response('');
	}
}
