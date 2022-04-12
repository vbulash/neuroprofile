<?php

use App\Models\Contract;
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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
			$table->string('number')->comment('Номер контракта');
			$table->string('invoice')->comment('Номер счёта');
			$table->date('start')->comment('Дата начала контракта');
			$table->date('end')->comment('Дата завершения контракта');
			$table->string('mkey')->comment('Мастер-ключ контракта');
			$table->integer('license_count')->comment('Количество лицензий по контракту');
			$table->string('url')->comment('URL сайта, который разрешен для размещения теста');
			$table->enum('status', [
				Contract::ACTIVE,
				Contract::INACTIVE,
				Contract::COMPLETE_BY_COUNT,
				Contract::COMPLETE_BY_DATE
			])->default(Contract::ACTIVE)->comment('Статус контракта');
			$table->unsignedBigInteger('client_id')->comment('Связанный клиент');
			$table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
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
        Schema::dropIfExists('contracts');
    }
};
