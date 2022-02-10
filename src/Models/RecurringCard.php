<?php

namespace Zorb\TBCPayment\Models;

class RecurringCard
{
	/**
	 * Saved card recId. Used for initiating payment with saved card.
	 *
	 * @var string
	 */
	public string $recId;

	/**
	 * Masked card PAN.
	 *
	 * @var string
	 */
	public string $cardMask;

	/**
	 * Date of expiry.
	 *
	 * @var string
	 */
	public string $expiryDate;

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->recId = $data['recId'] ?? null;
		$this->cardMask = $data['cardMask'] ?? null;
		$this->expiryDate = $data['expiryDate'] ?? null;
	}
}