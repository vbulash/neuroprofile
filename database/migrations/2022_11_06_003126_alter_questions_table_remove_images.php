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
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('image1');
			$table->dropColumn('image2');
			$table->dropColumn('value1');
			$table->dropColumn('value2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('image1')->comment('Файл первой картинки вопроса');
			$table->string('image2')->comment('Файл второй картинки вопроса');
			$table->string('value1', 2)->comment('Символ для первой картинки вопроса');
			$table->string('value2', 2)->comment('Символ для второй картинки вопроса');
        });
    }
};
