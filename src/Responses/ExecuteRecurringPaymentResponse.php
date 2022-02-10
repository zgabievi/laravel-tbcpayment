<?php

namespace Zorb\TBCPayment\Responses;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Zorb\TBCPayment\Enums\Currency;
use Zorb\TBCPayment\Models\Link;
use Zorb\TBCPayment\Models\RecurringCard;

class ExecuteRecurringPaymentResponse extends Response
{
	/**
	 * Payment id.
	 *
	 * @var string
	 */
	public string $payId;

	/**
	 * Payment status The following values are allowed: Succeeded WaitingConfirm Failed.
	 *
	 * @var string
	 */
	public string $status;

	/**
	 * Transaction currency (3 digit ISO code).
	 *
	 * @var ?Currency
	 */
	public ?Currency $currency;

	/**
	 * Transaction amount format: 0.00.
	 *
	 * @var float
	 */
	public float $amount;

	/**
	 * Links.
	 *
	 * @var Collection<Link>
	 */
	public Collection $links;

	/**
	 * Transaction id from UFC.
	 *
	 * @var string
	 */
	public string $transactionId;

	/**
	 * Pre-authorization status for given payment (true, false).
	 *
	 * @var string
	 */
	public string $preAuth;

	/**
	 * Saved card parameters: card recId, cardMask and expiryDate.
	 * If saving card wasn't required, null will be returned.
	 *
	 * @var ?RecurringCard
	 */
	public ?RecurringCard $recurringCard;

	/**
	 * Http status code.
	 *
	 * @var string
	 */
	public string $httpStatusCode;

	/**
	 * Developer message for logging in local system.
	 *
	 * @var string
	 */
	public string $developerMessage;

	/**
	 * Error message for user.
	 *
	 * @var string
	 */
	public string $userMessage;

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response->toPsrResponse());

		$currency = $this->json('currency');
		$recurringCard = $this->json('recurringCard');

		$this->payId = $this->json('payId');
		$this->status = $this->json('status');
		$this->currency = $currency ? Currency::from($currency) : null;
		$this->amount = $this->json('amount');
		$this->links = Collection::make($this->json('links', []))->map(fn(array $link) => new Link($link));
		$this->transactionId = $this->json('transactionId');
		$this->preAuth = $this->json('preAuth');
		$this->recurringCard = $recurringCard ? new RecurringCard($recurringCard) : null;
		$this->httpStatusCode = $this->json('httpStatusCode');
		$this->developerMessage = $this->json('developerMessage');
		$this->userMessage = $this->json('userMessage');
	}
}