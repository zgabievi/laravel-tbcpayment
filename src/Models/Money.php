<?php

namespace Zorb\TBCPayment\Models;

use Illuminate\Support\Facades\Log;
use Zorb\TBCPayment\Enums\Currency;

class Money
{
	/**
	 * @var ?float
	 */
	protected ?float $_amount;

	/**
	 * @var ?Currency
	 */
	protected ?Currency $_currency;

	/**
	 * @return static
	 */
	public static function make(): static
	{
		return new static();
	}

	/**
	 * @param float $amount
	 * @return $this
	 */
	public function amount(float $amount): static
	{
		$this->_amount = $amount;
		return $this;
	}

	/**
	 * @param Currency $currency
	 * @return $this
	 */
	public function currency(Currency $currency): static
	{
		$this->_currency = $currency;
		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$result = [
			'amount' => $this->_amount,
			'currency' => $this->_currency->value,
		];

		if (config('tbcpayment.debug')) {
			Log::debug('Money@toArray', $result);
		}

		return array_filter($result, fn($key, $value) => !is_null($value), ARRAY_FILTER_USE_BOTH);
	}
}