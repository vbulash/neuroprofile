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
        Schema::create('kinds', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Название типа вопроса');
			$table->tinyInteger('images')->comment('Количество изображений в вопросе');
			$table->tinyInteger('answers')->comment('Количество ответов в вопросе');
			$table->text('keys')->comment('Ключи вопросов');
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
        Schema::dropIfExists('kinds');
    }
};
