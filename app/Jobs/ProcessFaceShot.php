<?php

namespace App\Jobs;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Redis;

class ProcessFaceShot implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private object $content;

	public function __construct(object $content) {
		$this->content = $content;
		Log::info('Инициализация обработчика нейроосети');
	}

	public function handle() {
		$uuid = $this->content->uuid;
		$photo = $this->content->photo;
		$sex = $this->content->sex;

		$res = null;
		try {
			$res = Http::post(env('NEURAL_URL'), [
				'uuid' => $uuid,
				'photo' => $photo,
				'sex' => $sex,
			]);
		} catch (Exception $exc) {
			$message = 'Сервер нейросети недоступен, обработка нейросетью игнорируется';
			Log::error($message);
			$this->fail(
				new Exception($message, $exc->getCode(), $exc)
			);
			return response($message, 500);
		}

		$body = $res->json();
		// Log::debug(print_r($body, true));
		// Декодировать запрос
		$result = $body['result'];
		$uuid = $body['uuid'];
		$attention = $body['attention'];
		// TODO при необходимости анализировать здесь или в вызывателе code (обычно 200)
		// TODO разобрать $body['attention'] (base64) по готовности в теле возврата из нейросети
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
		if (isset($history)) { // История прохождения сохранена, можно добавлять результат работы нейросети
			$card = json_decode($history->card, true);
			$card['neural'] = [
				'result' => $neural,
				'attention' => $attention,
			];
			$history->update([
				'card' => json_encode($card)
			]);
			Log::info('Результат работы нейросети: сохранён');
		} else { // Новое прохождение, пока не сохранено, нужно будет добавить данные нейросети позже при сохранении
			Redis::set($uuid, json_encode([
				'result' => $neural,
				'attention' => $attention,
			]));
			Log::info('Результат работы нейросети: отложен до сохранения');
		}

		Log::info('Работа нейросети завершена');
	}
}
