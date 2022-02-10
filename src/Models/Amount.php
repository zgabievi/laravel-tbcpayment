<?php

namespace Zorb\TBCPayment\Models;

use Illuminate\Support\Facades\Log;
use Zorb\TBCPayment\Enums\Currency;

class Amount
{
	/**
	 * @var Currency
	 */
	protected Currency $_currency = Currency::GEL;

	/**
	 * @var ?float
	 */
	protected ?float $_total;

	/**
	 * @var ?float
	 */
	protected ?float $_subTotal;

	/**
	 * @var ?float
	 */
	protected ?float $_tax;

	/**
	 * @var ?float
	 */
	protected ?float $_shipping;

	/**
	 * @return static
	 */
	public static function make(): static
	{
		return new static();
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
	 * @param float $total
	 * @return $this
	 */
	public function total(float $total): static
	{
		$this->_total = $total;
		return $this;
	}

	/**
	 * @param float $subTotal
	 * @return $this
	 */
	public function subTotal(float $subTotal): static
	{
		$this->_subTotal = $subTotal;
		return $this;
	}

	/**
	 * @param float $tax
	 * @return $this
	 */
	public function tax(float $tax): static
	{
		$this->_tax = $tax;
		return $this;
	}

	/**
	 * @param float $shipping
	 * @return $this
	 */
	public function shipping(float $shipping): static
	{
		$this->_shipping = $shipping;
		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$result = [
			'currency' => $this->_currency->value,
			'total' => $this->_total,
			'subtotal' => $this->_subTotal,
			'tax' => $this->_tax,
			'shipping' => $this->_shipping,
		];

		if (config('tbcpayment.debug')) {
			Log::debug('Amount@toArray', $result);
		}

		return array_filter($result, fn($key, $value) => !is_null($value), ARRAY_FILTER_USE_BOTH);
	}
}