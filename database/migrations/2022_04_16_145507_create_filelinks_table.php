<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Счетчик ссылок на медиафайлы
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filelinks', function (Blueprint $table) {
            $table->id();
			$table->string('filename')->comment('Имя файла на сервере');
			$table->integer('linkcount')->default(0)->comment('Счетчик ссылок на файл');
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
        Schema::dropIfExists('filelinks');
    }
};
