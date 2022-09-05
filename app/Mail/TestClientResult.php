<?php

namespace App\Mail;

class TestClientResult extends TestResult
{

	public function build()
	{
		return $this->view('emails.tests.client_result')
			->subject(env('APP_NAME') . ' - результат тестирования респондента')
			;
	}
}
