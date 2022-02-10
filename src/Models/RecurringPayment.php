<?php

namespace Zorb\TBCPayment\Models;

use Illuminate\Support\Facades\Log;

class RecurringPayment
{
	/**
	 * @var ?bool
	 */
	protected ?bool $_preAuth;

	/**
	 * @var string
	 */
	protected string $_recId;

	/**
	 * @var ?string
	 */
	protected ?string $_merchantPaymentId;

	/**
	 * @var ?string
	 */
	protected ?string $_extra;

	/**
	 * @var ?string
	 */
	protected ?string $_extra2;

	/**
	 * @var Money
	 */
	protected Money $_money;

	/**
	 * @return static
	 */
	public static function make(): static
	{
		return new static();
	}

	/**
	 * @param bool $preAuth
	 * @return $this
	 */
	public function preAuth(bool $preAuth): static
	{
		$this->_preAuth = $preAuth;
		return $this;
	}

	/**
	 * @param string $recId
	 * @return $this
	 */
	public function recId(string $recId): static
	{
		$this->_recId = $recId;
		return $this;
	}

	/**
	 * @param string $merchantPaymentId
	 * @return $this
	 */
	public function merchantPaymentId(string $merchantPaymentId): static
	{
		$this->_merchantPaymentId = $merchantPaymentId;
		return $this;
	}

	/**
	 * @param string $extra
	 * @return $this
	 */
	public function extra(string $extra): static
	{
		$this->_extra = $extra;
		return $this;
	}

	/**
	 * @param string $extra2
	 * @return $this
	 */
	public function extra2(string $extra2): static
	{
		$this->_extra2 = $extra2;
		return $this;
	}

	/**
	 * @param Money $money
	 * @return $this
	 */
	public function money(Money $money): static
	{
		$this->_money = $money;
		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$result = [
			'preAuth' => $this->_preAuth,
			'recId' => $this->_recId,
			'merchantPaymentId' => $this->_merchantPaymentId,
			'extra' => $this->_extra,
			'extra2' => $this->_extra2,
			'money' => $this->_money->toArray(),
		];

		if (config('tbcpayment.debug')) {
			Log::debug('RecurringPayment@toArray', $result);
		}

		return array_filter($result, fn($key, $value) => !is_null($value), ARRAY_FILTER_USE_BOTH);
	}
}