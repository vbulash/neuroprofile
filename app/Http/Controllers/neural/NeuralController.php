<?php

namespace App\Http\Controllers\neural;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\License;
use Illuminate\Console\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Http;

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
		$uuid = $request->uuid;
		// Сохранить временную картинку .png для анализа формата
		$tmpimage = 'tmp/' . Str::uuid() . '.png';
		$image_parts = explode(";base64,", $request->photo);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];
		$image_base64 = base64_decode($image_parts[1]);
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

		return $status == 200 ?
			response('') :
			response()->json([
				'error' => $content
			], $status);
	}

	/**
	 * Инициирование нейросети
	 * @param Request $request
	 * @return bool
	 */
	public function netUp() {
		//$uuid = session('pkey');
		$uuid = 'pkey_628c8b6d245934.76213557';
		$source = sprintf("neural/%s/%s_1.png", $uuid, $uuid);
		$photo = base64_encode(Storage::get($source));
		$sex = 'M';
		$res = Http::post('http://localhost:6000', [
			'uuid' => $uuid,
			'photo' => $photo,
			'sex' => $sex,
		]);
		// Log::info('Ответ нейросети');
		// Log::info($res);
		$request = Request::create(route('neural.net.done'), 'POST', json_decode($res, true));
		$response = app()->handle($request);

		return true;
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
				'meansquare' => $item['mean-square'],
			];

		$license = License::where('pkey', $uuid)->first();
		$history = $license->history;
		if (isset($history)) {	// История прохождения сохранена, можно добавлять результат работы нейросети
			$card = json_decode($history->card, true);
			$card['neural'] = $neural;
			$history->update([
				'card' => json_encode($card)
			]);
			Log::info('Результат работы нейросети: сохранён');
			event(new ToastEvent('success', '', 'Результат работы нейросети: вычислен, сохранён'));
		} else {	// Новое прохождение, пока не сохранено, нужно будет добавить данные нейросети позже при сохранении
			session()->put('neural', $neural);
			Log::info('Результат работы нейросети: отложен до сохранения');
			event(new ToastEvent('success', '', 'Результат работы нейросети: вычислен, отложен до сохранения истории тестирования'));
		}

		return response('');
	}
}
