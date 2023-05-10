<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('blocks', function (Blueprint $table) {
			$table->boolean('show_title')->default(true)->comment('Видимость заголовка блока');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('blocks', function (Blueprint $table) {
			$table->dropColumn('show_title');
		});
	}
};