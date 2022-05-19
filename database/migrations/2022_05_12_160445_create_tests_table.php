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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Название теста');
			$table->integer('options')->comment('Опции теста');
			$table->longText('content')->comment('JSON со сложными параметрами теста');
			$table->string('key')->comment('Ключ теста');
			$table->boolean('paid')->default(false)->comment('Отметка платности теста');
			//
			$table->unsignedBigInteger('contract_id')->comment('Связанный контракт');
			$table->foreign('contract_id')->references('id')->on('contracts')->onDelete('no action');
			//
			$table->unsignedBigInteger('set_id')->comment('Связанный набор вопросов');
			$table->foreign('set_id')->references('id')->on('sets')->onDelete('no action');
			//
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
        Schema::dropIfExists('tests');
    }
};
