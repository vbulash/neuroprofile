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
	// Нейропрофили
	Route::resource('/profiles', 'ProfileController');
	Route::get('/profiles.data', 'ProfileController@getData')->name('profiles.index.data');
	Route::get('/profiles.select/{profile}', 'ProfileController@select')->name('profiles.select');
	// Блоки
	Route::resource('/blocks', 'BlockController');
	Route::get('/blocks.data', 'BlockController@getData')->name('blocks.index.data');
	Route::post('/blocks.up', 'BlockController@up')->name('blocks.up');
	Route::post('/blocks.down', 'BlockController@down')->name('blocks.down');
	// Контроллер текстовых блоков в цепочке обработки результатов тестирования
	//	Используются только несколько методов из полного ресурсного маршрута
	Route::get('/texts/create', 'blocks\TextController@create')->name('texts.create');
	Route::get('/texts/{text}/edit', 'blocks\TextController@edit')->name('texts.edit');
	// Контроллер ссылочных блоков в цепочке обработки результатов тестирования
	Route::get('/aliases.data', 'blocks\AliasController@getData')->name('aliases.index.data');
	Route::get('/aliases/create', 'blocks\AliasController@create')->name('aliases.create');
	Route::get('/aliases/{alias}/edit', 'blocks\AliasController@edit')->name('aliases.edit');
	// Прямая работа со ссылочными блоками
	Route::get('/aliaslists', 'blocks\AliasListController@index')->name('aliaslists.index');
	Route::get('/aliaslists.data', 'blocks\AliasListController@getData')->name('aliaslists.index.data');
	Route::get('/aliaslists/{alias}', 'blocks\AliasListController@show')->name('aliaslists.show');
	Route::delete('/aliaslists/{alias}', 'blocks\AliasListController@destroy')->name('aliaslists.destroy');
});

require __DIR__.'/auth.php';
