<?php

if (! function_exists('form')) {
	function form($formTemplate, int $mode, string $param): string {
		return match ($mode) {
			config('global.create') => $formTemplate::createTemplate()[$param],
			config('global.edit'), config('global.show') => $formTemplate->editTemplate()[$param],
			default => '',
		};
	}
}

if (! function_exists('classByContext')) {
	function classByContext(string $context) {
		return match ($context) {
			'client' => \App\Models\Client::class,
			'contract' => \App\Models\Contract::class,
			'fmptype' => \App\Models\FMPType::class,
			'profile' => \App\Models\Profile::class,
			'question' => \App\Models\Question::class,
			'role' => \App\Models\Role::class,
			'set' => \App\Models\Set::class,
			'user' => \App\Models\User::class
		};
	}
}
