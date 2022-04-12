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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('timetable_id')->comment('Связанный график стажировки');
			$table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
			$table->unsignedBigInteger('student_id')->comment('Связанный практикант');
			$table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
			$table->enum('status', ['Планируется', 'Выполняется', 'Закрыта'])->comment('Статус стажировки');
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
        Schema::dropIfExists('histories');
    }
};
