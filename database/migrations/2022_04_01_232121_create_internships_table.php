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
		// Стажировка
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
			$table->string('iname')->comment('Название стажировки');
			$table->enum('itype', ['Открытая стажировка', 'Закрытая стажировка'])->comment('Тип стажировки');
			$table->enum('status', ['Планируется', 'Выполняется', 'Закрыта'])->comment('Статус стажировки');
			$table->text('program')->comment('Программа стажировки');
			$table->unsignedBigInteger('employer_id')->comment('Связанный работодатель');
			$table->foreign('employer_id')->references('id')->on('employers');
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
        Schema::dropIfExists('internships');
    }
};
