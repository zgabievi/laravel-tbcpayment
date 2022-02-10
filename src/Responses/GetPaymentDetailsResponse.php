<?php

namespace Zorb\TBCPayment\Responses;

use Zorb\TBCPayment\Models\RecurringCard;
use Zorb\TBCPayment\Enums\PaymentMethod;
use Zorb\TBCPayment\Enums\PaymentStatus;
use Zorb\TBCPayment\Enums\ResultCode;
use Illuminate\Http\Client\Response;
use Zorb\TBCPayment\Enums\Currency;
use Illuminate\Support\Collection;
use Zorb\TBCPayment\Models\Link;

class GetPaymentDetailsResponse extends Response
{
	/**
	 * Payment id
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
	 * In case of pre-authorization, specifies confirmed amount for the transaction.
	 *
	 * @var float
	 */
	public float $confirmedAmount;

	/**
	 * Transaction.
	 *
	 * @var float
	 */
	public float $returnedAmount;

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
	 * Saved card parameters: card recId, cardMask and expiryDate.
	 * If saving card wasn't required, null will be returned.
	 *
	 * @var RecurringCard
	 */
	public RecurringCard $recurringCard;

	/**
	 * Payment method.
	 *
	 * @var PaymentMethod
	 */
	public PaymentMethod $paymentMethod;

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
	 * Business level transaction status description.
	 *
	 * @var ResultCode
	 */
	public ResultCode $resultCode;

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response->toPsrResponse());

		$currency = $this->json('currency');
		$recurringCard = $this->json('recurringCard');
		$paymentMethod = $this->json('paymentMethod');
		$resultCode = $this->json('resultCode');

		$this->payId = $this->json('payId');
		$this->status = $this->json('status');
		$this->currency = $currency ? Currency::from($currency) : null;
		$this->amount = $this->json('amount');
		$this->confirmedAmount = $this->json('confirmedAmount');
		$this->returnedAmount = $this->json('returnedAmount');
		$this->links = Collection::make($this->json('links', []))->map(fn(array $link) => new Link($link));
		$this->transactionId = $this->json('transactionId');
		$this->recurringCard = $recurringCard ? new RecurringCard($recurringCard) : null;
		$this->paymentMethod = $paymentMethod ? PaymentMethod::from($paymentMethod) : null;
		$this->preAuth = $this->json('preAuth');
		$this->httpStatusCode = $this->json('httpStatusCode');
		$this->developerMessage = $this->json('developerMessage');
		$this->userMessage = $this->json('userMessage');
		$this->resultCode = $resultCode ? ResultCode::from($resultCode) : null;
	}
}