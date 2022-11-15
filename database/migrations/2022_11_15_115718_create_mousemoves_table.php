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
        Schema::create('mousemoves', function (Blueprint $table) {
            $table->id();
			//
			$table->unsignedBigInteger('step_id')->comment('Связанный шаг истории');
			$table->foreign('step_id')->references('id')->on('historysteps')->cascadeOnDelete();
			//
			$table->bigInteger('time')->comment('Время движения');
			$table->double('x', 16, 14)->nullable()->comment('Координата X');
			$table->double('y', 16, 14)->nullable()->comment('Координата Y');
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
        Schema::dropIfExists('mousemoves');
    }
};
