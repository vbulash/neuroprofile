<?php

use Illuminate\Support\Facades\Artisan;
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
	// Контроллер блоков-изображений в цепочке обработки результатов тестирования
	// Используются только несколько методов из полного ресурсного маршрута
	Route::get('/images/create', 'blocks\ImageController@create')->name('images.create');
	Route::get('/images/{image}/edit', 'blocks\ImageController@edit')->name('images.edit');
	// Контроллер ссылочных блоков в цепочке обработки результатов тестирования
	Route::get('/aliases.data', 'blocks\AliasController@getData')->name('aliases.index.data');
	Route::get('/aliases/create', 'blocks\AliasController@create')->name('aliases.create');
	Route::get('/aliases/{alias}/edit', 'blocks\AliasController@edit')->name('aliases.edit');
	// Работа с блоками-предками
	Route::get('/parents', 'blocks\ParentController@index')->name('parents.index');
	Route::get('/parents.data', 'blocks\ParentController@getData')->name('parents.index.data');
	Route::get('/parents/{parent}', 'blocks\ParentController@show')->name('parents.show');
	Route::get('/parents/{parent}/edit', 'blocks\ParentController@edit')->name('parents.edit');
	Route::get('/parents.select/{parent}', 'blocks\ParentController@select')->name('parents.select');
	// Работа с блоками-потомками
	Route::get('/kids', 'blocks\KidController@index')->name('kids.index');
	Route::get('/kids.data', 'blocks\KidController@getData')->name('kids.index.data');
	Route::get('/kids.unlink/{kid}', 'blocks\KidController@unlink')->name('kids.unlink');
	// Копирование блоков
	Route::get('/clones', 'blocks\CloneController@index')->name('clones.index');
	Route::get('/clones.data', 'blocks\CloneController@getData')->name('clones.index.data');
	Route::get('/clones/{block}', 'blocks\CloneController@show')->name('clones.show');
	Route::get('/clones/{source}/clone', 'blocks\CloneController@clone')->name('clones.clone');

	// Тесты
	// Wizard добавления / редактирования / просмотра
	Route::get('/steps/play/{mode}/{test?}', 'tests\StepController@play')->name('steps.play');
	Route::get('/steps/back', 'tests\StepController@back')->name('steps.back');
	Route::get('/steps/next', 'tests\StepController@next')->name('steps.next');
	Route::get('/steps/close', 'tests\StepController@close')->name('steps.close');
	Route::post('/steps/finish', 'tests\StepController@finish')->name('steps.finish');
	// Тесты
	Route::resource('/tests', 'TestController');
	Route::get('/tests.data', 'TestController@getData')->name('tests.index.data');
});

// Служебные маршруты
Route::get('/optimize-clear', function () {
	$exitCode = Artisan::call('optimize:clear');
	echo('Сделана полная оптимизация. Код завершения = '. $exitCode);
});

require __DIR__.'/auth.php';
