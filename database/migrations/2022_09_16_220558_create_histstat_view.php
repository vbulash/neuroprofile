<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
	{
		DB::statement(<<<'EOS'
CREATE VIEW histstat AS
SELECT
    date_format(history.done, '%e.%m.%Y') AS day,
    count(history.id) AS total,
    count(if((history.paid = TRUE), history.id, NULL)) AS paid
FROM history
GROUP BY cast(history.done AS date)
ORDER BY cast(history.done AS date) DESC
EOS
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		DB::statement('DROP VIEW histstat');
	}
};
