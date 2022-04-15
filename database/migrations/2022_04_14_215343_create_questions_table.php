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
		// Вопросы теста
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
			$table->integer('sort_no')->comment('Порядок сортировки вопроса в наборе');
			$table->boolean('learning')->default(false)->comment('Признак учебного вопроса');
			$table->integer('timeout')->default(0)->comment('Таймаут прохождения вопроса');
			// Картинки
			$table->string('image1')->comment('Файл первой картинки вопроса');
			$table->string('image2')->comment('Файл второй картинки вопроса');
			$table->string('value1', 2)->comment('Символ для первой картинки вопроса');
			$table->string('value2', 2)->comment('Символ для второй картинки вопроса');
			//
			$table->unsignedBigInteger('set_id')->comment('Связанный набор вопросов');
			$table->foreign('set_id')->references('id')->on('sets')->cascadeOnDelete();
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
        Schema::dropIfExists('questions');
    }
};
