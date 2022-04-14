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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
			$table->string('pkey')->comment('Программный ключ лицензии');
			$table->tinyInteger('status')->comment('Статус лицензии');

			$table->unsignedBigInteger('contract_id')->comment('Связанный контракт');
			$table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
			$table->unsignedBigInteger('user_id')->comment('Связанный пользователь')->nullable();
			$table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
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
        Schema::dropIfExists('licenses');
    }
};
