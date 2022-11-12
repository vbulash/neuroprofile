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
	// Оплата
	Route::get('/payment.result', 'PlayerController@paymentResult')->name('payment.result');
	Route::get('/payment.success', 'PlayerController@paymentSuccess')->name('payment.success');
	Route::get('/payment.fail', 'PlayerController@paymentFail')->name('payment.fail');
	// Окружение
	Route::get('/phpinfo', fn() => phpinfo())->name('api.phpinfo');
});

// Нейросеть
Route::group(['namespace' => 'App\Http\Controllers\neural'], function () {
	Route::post('/shot.done', 'NeuralController@shotDone')->name('neural.shot.done');
	Route::post('/net.done', 'NeuralController@netDone')->name('neural.net.done');
});
