<?php

namespace App\Http\Controllers\tests;

use App\Models\Titleable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

interface Step extends Titleable
{
	public function getTitle(): string;
	public function getStoreRules(): array;
	public function getStoreAttributes(): array;
	public function create(Request $request);
	public function edit(Request $request);
	public function store(array $data): bool;
	public function update(array $data): bool;
}
