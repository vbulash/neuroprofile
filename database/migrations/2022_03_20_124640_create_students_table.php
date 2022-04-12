<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
			$table->string('lastname')->comment('Фамилия');
			$table->string('firstname')->comment('Имя');
			$table->string('surname')->comment('Отчество')->nullable();
			$table->enum('sex', ['Мужской', 'Женский'])->comment('Пол')->nullable();
			$table->datetime('birthdate')->comment('Дата рождения');
			$table->string('phone')->comment('Телефон');
			$table->string('email')->comment('Электронная почта');
			$table->string('parents')->comment('ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать')->nullable();
			$table->string('parentscontact')->comment('Контактные телефоны родителей или опекунов')->nullable();
			$table->string('passport')->comment('Данные паспорта (серия, номер, кем и когда выдан)')->nullable();
			$table->string('address')->comment('Адрес проживания')->nullable();
			$table->string('institutions')->comment('Учебное заведение (на момент заполнения)')->nullable();
			$table->string('grade')->comment('Класс / группа (на момент заполнения)')->nullable();
			$table->text('hobby')->comment('Увлечения (хобби)')->nullable();
			$table->string('hobbyyears')->comment('Как давно занимается хобби (лет)?')->nullable();
			//$table->text('hobbyachievements')->comment('Есть ли достижения, полученные благодаря хобби?')->nullable();
			$table->text('contestachievements')->comment('Участие в конкурсах, олимпиадах. Достижения')->nullable();
			$table->text('dream')->comment('Чем хочется заниматься в жизни?')->nullable();
			$table->string('documents')->comment('Документы')->nullable();
			$table->unsignedBigInteger('user_id')->comment('Связанный пользователь')->nullable();
			$table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
