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
        Schema::table('kinds', function (Blueprint $table) {
            $table->integer('phone')->comment('Изображений в строке (телефон)');
			$table->integer('tablet')->comment('Изображений в строке (планшет)');
			$table->integer('desktop')->comment('Изображений в строке (ноутбук / десктоп)');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kinds', function (Blueprint $table) {
            $table->dropColumn('phone');
			$table->dropColumn('tablet');
			$table->dropColumn('desktop');
        });
    }
};
