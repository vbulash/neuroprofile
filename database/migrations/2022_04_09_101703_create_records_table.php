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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Наименование записи');
			$table->text('report')->comment('Отчёт по записи')->nullable();
			$table->enum('status', ['Планируется', 'Выполняется', 'Закрыта'])->comment('Статус стажировки');
			$table->unsignedBigInteger('history_id')->comment('Связанная история');
			$table->foreign('history_id')->references('id')->on('histories')->cascadeOnDelete();
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
        Schema::dropIfExists('records');
    }
};
