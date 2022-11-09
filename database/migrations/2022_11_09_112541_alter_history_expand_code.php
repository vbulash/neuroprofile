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
        Schema::table('history', function (Blueprint $table) {
            $table->string('code')->change();
        });

		Schema::table('historysteps', function (Blueprint $table) {
			$table->string('key')->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history', function (Blueprint $table) {
            $table->string('code', 2)->change();
        });

		Schema::table('historysteps', function (Blueprint $table) {
			$table->string('key', 2)->change();
		});
    }
};
