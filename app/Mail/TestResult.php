<?php

namespace App\Mail;

use App\Models\History;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestResult extends Mailable
{
    use Queueable, SerializesModels;

	public History $history;
	public Collection $blocks;
	public array $card;
	public Profile $profile;
	public ?object $branding;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(History $history, Collection $blocks, array $card, Profile $profile, ?object $branding)
    {
		$this->history = $history;
		$this->blocks = $blocks;
		$this->card = $card;
		$this->profile = $profile;
		$this->branding = $branding;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
	{
		return $this->view('emails.tests.result')
			->subject(env('APP_NAME') . ' - индивидуальный результат тестирования')
			;
    }
}
