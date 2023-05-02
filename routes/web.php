<?php

use App\Http\Controllers\ClientAdminController;
use App\Http\Controllers\ProfileController;
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
	// Администраторы
	Route::resource('/admins', 'AdminController');
	Route::get('/admins.data', 'AdminController@getData')->name('admins.index.data');
	// Администраторы клиентов
	Route::resource('/adminclients', 'AdminClientController');
	Route::get('/adminclients.data', 'AdminClientController@getData')->name('adminclients.index.data');
	// Клиенты
	Route::resource('/clients', 'ClientController');
	Route::get('/clients.data', 'ClientController@getData')->name('clients.index.data');
	Route::get('/clients.select/{client}/{kind}', 'ClientController@select')->name('clients.select');
	// Администраторы клиентов
	Route::resource('/clients.users', 'ClientAdminController');
	Route::get('/clients.users.data/{client}', 'ClientAdminController@getData')->name('clients.users.index.data');
	Route::post('/clients.users.attach', [ClientAdminController::class, 'attach'])->name('clients.users.attach');
	Route::post('/clients.users.detach', [ClientAdminController::class, 'detach'])->name('clients.users.detach');
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
	Route::get('/questions.select/{question}', 'QuestionController@select')->name('questions.select');
	Route::post('/questions.up', 'QuestionController@up')->name('questions.up');
	Route::post('/questions.down', 'QuestionController@down')->name('questions.down');
	Route::post('/questions.duplicate', 'QuestionController@duplicate')->name('questions.duplicate');
	// Изображения
	Route::resource('/parts', 'PartController');
	Route::get('/parts.data', 'PartController@getData')->name('parts.index.data');
	// Типы вопросов
	Route::resource('/kinds', 'KindController');
	Route::get('/kinds.data', 'KindController@getData')->name('kinds.index.data');

	// Блок обработки результатов тестирования
	// Тип описания
	Route::resource('/fmptypes', 'FMPTypeController');
	Route::get('/fmptypes.data', 'FMPTypeController@getData')->name('fmptypes.index.data');
	Route::get('/fmptypes.select/{fmptype}', 'FMPTypeController@select')->name('fmptypes.select');
	Route::get('/fmtypes.copy/{fmptype}', 'FMPTypeController@copy')->name('fmptypes.copy');
	// Нейропрофили
	Route::resource('/profiles', 'ProfileController');
	Route::get('/profiles.data', 'ProfileController@getData')->name('profiles.index.data');
	Route::get('/profiles.select/{profile}', 'ProfileController@select')->name('profiles.select');
	Route::post('/profiles.export', [ProfileController::class, 'export'])->name('profiles.export');
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
	Route::get('/kids/{kid}/edit', 'blocks\KidController@edit')->name('kids.edit');
	Route::get('/kids.unlink/{kid}', 'blocks\KidController@unlink')->name('kids.unlink');
	Route::get('/kids.follow/{kid}', 'blocks\KidController@follow')->name('kids.follow');
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
	Route::get('/tests.list', 'TestController@list')->name('tests.list');

	// История прохождения тестирования
	Route::resource('/history', 'HistoryController');
	Route::get('/history.data', 'HistoryController@getData')->name('history.index.data');
	Route::get('/history.mail', 'PlayerController@mail')->name('history.mail');
	Route::get('history.export', 'HistoryController@export')->name('history.export');
});

// Служебные маршруты
Route::get('/optimize-clear', function () {
	$exitCode = Artisan::call('optimize:clear');
	echo ('Сделана полная оптимизация. Код завершения = ' . $exitCode);
});

// Плеер
Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => 'restore.session'], function () {
	// Проверка
	Route::get('/player.iframe', 'PlayerController@iframe')->name('player.iframe');
	//
	Route::get('/player.index', 'PlayerController@index')->name('player.index');
	Route::get('/player.play/{mkey?}/{test?}', 'PlayerController@play')->name('player.play');
	Route::get('/player.card', 'PlayerController@card')->name('player.card');
	// Маршруты сохранения предварительной информации (из карточек)
	Route::get('/player.pkey', 'PlayerController@store_pkey')->name('player.pkey');
	Route::get('/player.full', 'PlayerController@store_full_card')->name('player.full');
	// Фото лица
	Route::get('/player.face', 'PlayerController@face')->name('player.face');
	// Калибровка айтрекинга
	Route::get('/player.eye', 'PlayerController@eye')->name('player.eye');

	Route::get('/player.body2', 'PlayerController@body2')->name('player.body2');
	Route::post('/player/body2.store', 'PlayerController@body2_store')->name('player.body2.store');
	Route::get('/player.calculate/{history_id}', 'PlayerController@calculate')->name('player.calculate');
	Route::get('/player.mail/{history_id}', 'PlayerController@mail')->name('player.mail');

	Route::get('/player.policy/{document}/{mail?}', 'PlayerController@showDocument')->name('player.policy');
});

require __DIR__ . '/auth.php';