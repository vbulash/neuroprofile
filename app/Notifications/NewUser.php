<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class NewUser extends Notification {
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable) {
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		$roles = $notifiable->getRoleNames()->join(" / ");
		return (new MailMessage)
			->subject("Создан новый пользователь")
			->line("Создан новый пользователь \"{$notifiable->name}\" с ролью \"{$roles}\".")
			->line("В целях безопасности пароль пользователя никогда не пересылается по почте. " .
				sprintf("Если вам предварительно не сообщили или вы забудете свой пароль - воспользуйтесь функцией его сброса на диалоге входа в \"%s\"и получите дальнейшие инструкции в электронной почте.", env('APP_NAME')));
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toArray($notifiable) {
		return [
			//
		];
	}
}