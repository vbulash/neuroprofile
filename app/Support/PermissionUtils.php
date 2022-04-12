<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Boolean;
use Spatie\Permission\Models\Permission;

class PermissionUtils
{
	public static function can(string $right): bool {
		$permissions = Auth::user()->getAllPermissions()
			->filter(function ($value, $key) use ($right) {
				return Str::contains($value, $right);
			});
		return $permissions->count() != 0;
	}

	public static function getPermissionIDs(string $right): ?array {
		$IDs = [];
		$permissions = Auth::user()->getAllPermissions()
			->each(function ($value, $key) use ($right, &$IDs) {
				if (Str::contains($value->name, $right)) {
					$parts = explode('.', $value->name);
					if (count($parts) == 3)
						$IDs[] = $parts[2];
				}
			});
		return $IDs;
	}
}
