<?php

namespace App\View\Components\Robokassa;

use App\Models\History;

trait RobokassaCore
{
	protected string $merchant;
	protected string $password;
	protected int $invoice;
	protected int $sum;
	protected string $description;
	protected bool $mail = false;
	protected ?string $email = null;

	/**
	 * @return string
	 */
	public function getMerchant(): string
	{
		return $this->merchant;
	}

	/**
	 * @param string $merchant
	 */
	public function setMerchant(string $merchant): void
	{
		$this->merchant = $merchant;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * @return int
	 */
	public function getInvoice(): int
	{
		return $this->invoice;
	}

	/**
	 * @param int $invoice
	 */
	public function setInvoice(int $invoice): void
	{
		$this->invoice = $invoice;
	}

	/**
	 * @return int
	 */
	public function getSum(): int
	{
		return $this->sum;
	}

	/**
	 * @param int $sum
	 */
	public function setSum(int $sum): void
	{
		$this->sum = $sum;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description): void
	{
		$this->description = urlencode($description);
	}

	/**
	 * @return bool
	 */
	public function isMail(): bool
	{
		return $this->mail;
	}

	/**
	 * @param bool $mail
	 */
	public function setMail(bool $mail): void
	{
		$this->mail = $mail;
	}

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * @param string|null $email
	 */
	public function setEmail(?string $email): void
	{
		$this->email = $email;
	}

	protected function init(History $history, string $description,
							int $invoice = 0, bool $mail = false, ?string $email = null): void
	{
		$card = json_decode($history->card);
		$this->setDescription($description);
		$this->setInvoice($invoice == 0 ? $history->getKey() : $invoice);
		$this->setMail($mail);
		$this->setEmail($email ?? ($card->email ?? null));

		// Умолчания
		$this->setMerchant(env('ROBOKASSA_MERCHANT'));
		$this->setPassword(env('ROBOKASSA_PASSWORD'));
		$this->setSum(env('ROBOKASSA_SUM'));

		// Если есть кастомная оплата
		if(!isset($history->test->content)) return;
		$content = json_decode($history->test->content, true);
		if(!isset($content['robokassa'])) return;
		$robokassa = $content['robokassa'];

		$this->setMerchant($robokassa['merchant']);
		$this->setPassword($robokassa['password']);
		$this->setSum($robokassa['sum']);
	}

}
