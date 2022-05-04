<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Тип описания
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fmptypes', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Наименование типа описания');
			$table->tinyInteger('cluster')->comment('Разовидность типа описания: ФМП / нейройкластер');
			$table->boolean('active')->default(false)->comment('Активность типа описания');
			$table->tinyInteger('limit')->default(16)->comment('Плановое количество нейропрофилей');
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
        Schema::dropIfExists('fmptypes');
    }
};
