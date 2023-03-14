<?php

if (!function_exists('form')) {
	function form($formTemplate, int $mode, string $param): string {
		return match ($mode) {
			config('global.create') => $formTemplate::createTemplate()[$param],
			config('global.edit'), config('global.show') => $formTemplate->editTemplate()[$param],
			default => '',
		};
	}
}

if (!function_exists('classByContext')) {
	function classByContext(string $context) {
		return match ($context) {
			'alias' => \App\Models\Block::class,
			'client' => \App\Models\Client::class,
			'contract' => \App\Models\Contract::class,
			'fmptype' => \App\Models\FMPType::class,
			'parent' => \App\Models\Block::class,
			'profile' => \App\Models\Profile::class,
			'question' => \App\Models\Question::class,
			'role' => \App\Models\Role::class,
			'set' => \App\Models\Set::class,
			'admin' => \App\Models\Admin::class,
			'history' => \App\Models\History::class,
			'kind' => \App\Models\Kind::class,
			'part' => \App\Models\Part::class,
		};
	}
}

if (!function_exists('createDropdown')) {
	function createDropdown(string $title, array $items) {
		$out = '';
		if (count($items) == 0)
			return '';
		if (count($items) == 1) {
			$item = $items[0];
			if (isset($item['click'])) {
				return sprintf(<<<'EOT'
<a href="javascript:void(0)" onclick="%s" class="btn btn-primary btn-sm float-left ms-1">
	<i class="%s"></i> %s
</a>
EOT, $item['click'], $item['icon'] ?? "fas fa-check", $item['title']);
			} else {
				return sprintf(<<<'EOT'
<a href="%s" class="btn btn-primary btn-sm float-left ms-1">
	<i class="%s"></i> %s
</a>
EOT, $item['link'], $item['icon'] ?? "fas fa-check", $item['title']);
			}
		} else {
			foreach ($items as $item) {
				if ($item['type'] == 'divider')
					$out .= "<div class=\"dropdown-divider\"></div>\n";
				elseif (isset($item['click']))
					$out .= sprintf("<li><a class=\"dropdown-item\" href=\"javascript:void(0)\" onclick=\"%s\"><i class=\"%s\"></i> %s</a></li>\n",
						$item['click'], $item['icon'] ?? "fas fa-check", $item['title']);
				else
					$out .= sprintf("<li><a class=\"dropdown-item\" href=\"%s\"><i class=\"%s\"></i> %s</a></li>\n",
						$item['link'], $item['icon'] ?? "fas fa-check", $item['title']);
			}
			return sprintf(<<<'EOT'
<div class="dropdown">
	<button type="button" class="btn btn-primary dropdown-toggle show actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		%s
    </button>
	<div class="dropdown-menu" aria-labelledby="dropdown-dropup-primary" data-popper-placement="top-start">
		%s
	</div>
</div>
EOT, $title, $out);
		}
	}
}