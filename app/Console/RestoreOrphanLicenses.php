<?php

namespace App\Console;

use App\Models\License;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestoreOrphanLicenses
{
	public function __invoke(): void
	{
		$updated = DB::update(<<<EOU
UPDATE licenses
SET licenses.status = ?
WHERE licenses.id NOT IN (
    SELECT license_id FROM history
    )
  AND
	licenses.status <> ?
EOU,
			[License::FREE, License::FREE]);
		if ($updated > 0)
			Log::info(sprintf("Восстановлено зависших лицензий: %d", $updated));
	}
}
