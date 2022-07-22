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
        Schema::create('historysteps', function (Blueprint $table) {
            $table->id();
			$table->foreignId('history_id')->constrained('history')->cascadeOnDelete();
			$table->foreignId('question_id')->constrained()->cascadeOnDelete();
			$table->timestamp('done')->nullable();
			$table->string('key', 2)->nullable();
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
        Schema::dropIfExists('historysteps');
    }
};
