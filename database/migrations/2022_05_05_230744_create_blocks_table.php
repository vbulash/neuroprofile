<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BlockType;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
			$table->integer('sort_no')->default(0)->comment('Номер по порядку');
			$table->string('name')->comment('Название блока');
			$table->tinyInteger('type')->default(BlockType::Text->value)->comment('Тип блока');
			$table->text('full')->nullable()->comment('Полный (платный) текст блока');
			$table->text('short')->nullable()->comment('Краткий (бесплатный) текст блока');
			//
			$table->unsignedBigInteger('block_id')->nullable()->comment('Родительский блок');
			$table->foreign('block_id')->references('id')->on('blocks')->onDelete('no action');	// Родительский блок при наличии потомков удалять нельзя
			//
			$table->unsignedBigInteger('profile_id')->comment('Связанный нейропрофиль');
			$table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete();
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
        Schema::dropIfExists('blocks');
    }
};
