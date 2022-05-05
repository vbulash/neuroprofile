<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth', 'restore.session']], function () {
	// Главная
	Route::get('/', 'MainController@index')->name('dashboard');
	// Пользователи
	Route::resource('/users', 'UserController');
	Route::get('/users.data', 'UserController@getData')->name('users.index.data');
	// Клиенты
	Route::resource('/clients', 'ClientController');
	Route::get('/clients.data', 'ClientController@getData')->name('clients.index.data');
	Route::get('/clients.select/{client}', 'ClientController@select')->name('clients.select');
	// Контракты
	Route::resource('/contracts', 'ContractController');
	Route::get('/conntracts.data', 'ContractController@getData')->name('contracts.index.data');
	Route::get('/contracts.select/{contract}', 'ContractController@select')->name('contracts.select');
	Route::get('/contracts.info', 'ContractController@info')->name('contracts.info');
	Route::get('/contracts.licenses.export/{contract}', 'ContractController@licensesExport')->name('contracts.licenses.export');
	// Наборы вопросов
	Route::resource('/sets', 'SetController');
	Route::get('/sets.data', 'SetController@getData')->name('sets.index.data');
	Route::get('/sets.select/{set}', 'SetController@select')->name('sets.select');
	// Вопросы
	Route::resource('/questions', 'QuestionController');
	Route::get('/questions.data', 'QuestionController@getData')->name('questions.index.data');
	Route::post('/questions.up', 'QuestionController@up')->name('questions.up');
	Route::post('/questions.down', 'QuestionController@down')->name('questions.down');

	// Блок обработки результатов тестирования
	// Тип описания
	Route::resource('/fmptypes', 'FMPTypeController');
	Route::get('/fmptypes.data', 'FMPTypeController@getData')->name('fmptypes.index.data');
	Route::get('/fmptypes.select/{fmptype}', 'FMPTypeController@select')->name('fmptypes.select');
	//
	Route::resource('/profiles', 'ProfileController');
	Route::get('/profiles.data', 'ProfileController@getData')->name('profiles.index.data');
	Route::get('/profiles.select/{profile}', 'ProfileController@select')->name('profiles.select');
});

require __DIR__.'/auth.php';
