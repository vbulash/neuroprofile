<?php

namespace App\View\Components\Robokassa;

use App\Models\History;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Closure;

class Frame extends Component
{
	use RobokassaCore;

	/**
	 * Create a new component instance.
	 *
	 * @param History $history
	 * @param string $description
	 * @param int $invoice
	 * @param bool $mail
	 * @param string|null $email
	 */
    public function __construct(History $history, string $description,
								int $invoice = 0, bool $mail = false, ?string $email = null)
    {
		$this->init($history, $description, $invoice, $mail, $email);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render(): View|string|Closure
	{
        return view('components.robokassa.frame');
    }
}
