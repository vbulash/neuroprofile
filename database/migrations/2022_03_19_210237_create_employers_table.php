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
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Наименование организации');
			$table->string('contact')->comment('Контактное лицо')->nullable();
			$table->string('address')->comment('Фактический адрес')->nullable();
			$table->string('phone')->comment('Телефон')->nullable();
			$table->string('email')->comment('Электронная почта')->nullable();
			$table->string('inn')->comment('ИНН')->nullable();
			$table->string('kpp')->comment('КПП')->nullable();
			$table->string('ogrn')->comment('ОГРН / ОГРНИП')->nullable();
			$table->string('official_address')->comment('Юридический адрес')->nullable();
			$table->string('post_address')->comment('Почтовый адрес')->nullable();
			$table->text('description')->comment('Краткое описание организации (основная деятельность)')->nullable();
			$table->text('expectation')->comment('Какие результаты ожидаются от практикантов / выпускников?')->nullable();
			$table->string('nda')->comment('Соглашение о неразглашении информации')->nullable();
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
        Schema::dropIfExists('employers');
    }
};
