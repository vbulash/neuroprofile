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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Наименование клиента');
			$table->string('inn')->comment('ИНН');
			$table->string('ogrn')->comment('ОГРН / ОГРНИП');
			$table->string('address')->comment('Фактический адрес клиента');
			$table->string('phone')->comment('Телефон для связи')->nullable();
			$table->string('email')->comment('Электронная почта');
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
        Schema::dropIfExists('clients');
    }
};
