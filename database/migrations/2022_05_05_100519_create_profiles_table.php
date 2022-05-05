<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Нейропрофиль
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
			$table->string('code', 10)->comment('Код нейропрофиля');
			$table->string('name')->comment('Название нейропрофиля');
			//
			$table->unsignedBigInteger('fmptype_id')->comment('Связанный тип описания');
			$table->foreign('fmptype_id')->references('id')->on('fmptypes')->cascadeOnDelete();
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
        Schema::dropIfExists('profiles');
    }
};
