<?php

namespace Zorb\TBCPayment\Responses;

use Zorb\TBCPayment\Enums\PaymentStatus;
use Illuminate\Http\Client\Response;
use Zorb\TBCPayment\Enums\Currency;
use Illuminate\Support\Collection;
use Zorb\TBCPayment\Models\Link;

class CreatePaymentResponse extends Response
{
	/**
	 * Payment id.
	 *
	 * @var string
	 */
	public string $payId;

	/**
	 * Payment status The following values are allowed: Created, Processing, Succeeded, Failed, Expired, WaitingConfirm.
	 *
	 * @var PaymentStatus
	 */
	public PaymentStatus $status;

	/**
	 * Transaction currency (3 digit ISO code).
	 *
	 * @var ?Currency
	 */
	public ?Currency $currency;

	/**
	 * Transaction amount.
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
	 * In case of recurring payment registration, recId of saved card.
	 * Rec Id should be used for executing recurring payment.
	 *
	 * @var string
	 */
	public string $recId;

	/**
	 * Pre-authorization status for given payment (true, false).
	 *
	 * @var bool
	 */
	public bool $preAuth;

	/**
	 * Http status code.
	 *
	 * @var int
	 */
	public int $httpStatusCode;

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
	 * Payment initiation expiration time in minutes.
	 *
	 * @var int
	 */
	public int $expirationMinutes;

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response->toPsrResponse());

		$currency = $this->json('currency');

		$this->payId = $this->json('payId');
		$this->status = $this->json('status');
		$this->currency = $currency ? Currency::from($currency) : null;
		$this->amount = $this->json('amount');
		$this->links = Collection::make($this->json('links', []))->map(fn(array $link) => new Link($link));
		$this->transactionId = $this->json('transactionId');
		$this->recId = $this->json('recId');
		$this->preAuth = $this->json('preAuth');
		$this->httpStatusCode = $this->json('httpStatusCode');
		$this->developerMessage = $this->json('developerMessage');
		$this->userMessage = $this->json('userMessage');
		$this->expirationMinutes = $this->json('expirationMinutes');
	}
}