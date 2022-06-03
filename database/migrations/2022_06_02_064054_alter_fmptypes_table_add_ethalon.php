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
		Schema::table('fmptypes', function (Blueprint $table) {
			$table->boolean('ethalon')->default(false)->comment('Эталонный тип описания');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('fmptypes', function (Blueprint $table) {
			$table->dropColumn('ethalon');
		});
    }
};
