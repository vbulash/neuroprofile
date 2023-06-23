<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
	return $request->user();
});

// Утилиты
Route::group(['namespace' => 'App\Http\Controllers'], function () {
	// Генерация случайного пароля длиной length символов
	Route::post('/get.password/{length}', 'HelperController@generatePassword')->name('api.get.password');
	// Перевод в режим обслуживания
	Route::get('/down', function () {
		Artisan::call('down');
		abort(503);
	}
	)->name('api.down');
	Route::post('/ping', fn() => 'pong')->name('api.ping');
	// Оплата
	Route::get('/payment.result', 'PlayerController@paymentResult')->name('payment.result');
	Route::get('/payment.success', 'PlayerController@paymentSuccess')->name('payment.success');
	Route::get('/payment.fail', 'PlayerController@paymentFail')->name('payment.fail');
	// Окружение
	Route::get('/phpinfo', fn() => phpinfo())->name('api.phpinfo');
});

// Плеер
Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => 'restore.session'], function () {
	Route::get('/player.play/{mkey?}/{test?}', 'PlayerController@play')->name('player.play2');
	Route::get('/player.body2', 'PlayerController@body2')->name('player.body2');
	Route::post('/player/body2.store', 'PlayerController@body2_store')->name('player.body2.store');
	Route::get('/player.calculate/{history_id}', 'PlayerController@calculate')->name('player.calculate');
	Route::get('/player.mail/{history_id}', 'PlayerController@mail')->name('player.mail');
});

// Нейросеть
Route::group(['namespace' => 'App\Http\Controllers\neural'], function () {
	Route::post('/shot.done', 'NeuralController@shotDone')->name('neural.shot.done');
	// Route::post('/net.up', 'NeuralController@netUp')->name('neural.net.up');
	Route::post('/net.done', 'NeuralController@netDone')->name('neural.net.done');
});