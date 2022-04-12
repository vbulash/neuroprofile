<?php

namespace App\Notifications\e2s;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StartInternshipNotification extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		$subject = '';
		$lines = [];
		$lines[] = 'Уважаемый (уважаемая) ' . $notifiable->student->getTitle(). '!';
		switch ($notifiable->status) {
			case 'Планируется':
				$subject = 'Запланирована новая стажировка';
				$lines[] = 'Для вас запланирована новая стажировка со следующими параметрами:';
				break;
			case 'Выполняется':
				$subject = 'Начата стажировка';
				$lines[] = 'Ваша стажировка началась. Напоминаем её параметры:';
				break;
			case 'Закрыта':
				$subject = 'Стажировка завершена';
				$lines[] = 'Ваша стажировка завершена. Напоминаем её параметры:';
				break;
		}
		$lines[] = 'Работодатель: "' . $notifiable->timetable->internship->employer->getTitle() . '"';
		$lines[] = 'Стажировка: "' . $notifiable->timetable->internship->getTitle() . '"';
		$lines[] = 'График стажировки: ' . $notifiable->timetable->getTitle();

		$message = (new MailMessage)->subject($subject);
		foreach ($lines as $line)
			$message = $message->line($line);
		return $message;
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
