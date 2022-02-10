<?php

namespace Zorb\TBCPayment\Models;

use Illuminate\Support\Facades\Log;

class InstallmentProduct
{
	/**
	 * @var ?string
	 */
	protected ?string $_name;

	/**
	 * @var float
	 */
	protected float $_price;

	/**
	 * @var int
	 */
	protected int $_quantity = 1;

	/**
	 * @return static
	 */
	public static function make(): static
	{
		return new static();
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function name(string $name): static
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * @param float $price
	 * @return $this
	 */
	public function price(float $price): static
	{
		$this->_price = $price;
		return $this;
	}

	/**
	 * @param int $quantity
	 * @return $this
	 */
	public function quantity(int $quantity = 1): static
	{
		$this->_quantity = $quantity;
		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$result = [
			'name' => $this->_name,
			'price' => $this->_price,
			'quantity' => $this->_quantity,
		];

		if (config('tbcpayment.debug')) {
			Log::debug('InstallmentProduct@toArray', $result);
		}

		return array_filter($result, fn($key, $value) => !is_null($value), ARRAY_FILTER_USE_BOTH);
	}
}