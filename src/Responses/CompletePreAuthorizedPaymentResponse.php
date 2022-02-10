<?php

namespace Zorb\TBCPayment\Responses;

use Illuminate\Http\Client\Response;

class CompletePreAuthorizedPaymentResponse extends Response
{
	/**
	 * @var string
	 */
	public string $status;

	/**
	 * @var float
	 */
	public float $amount;

	/**
	 * @var float
	 */
	public float $confirmedAmount;

	/**
	 * @var int
	 */
	public int $httpStatusCode;

	/**
	 * @var string
	 */
	public string $developerMessage;

	/**
	 * @var string
	 */
	public string $userMessage;

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response->toPsrResponse());

		$this->status = $this->json('status');
		$this->amount = $this->json('amount');
		$this->confirmedAmount = $this->json('confirmedAmount');
		$this->httpStatusCode = $this->json('httpStatusCode');
		$this->developerMessage = $this->json('developerMessage');
		$this->userMessage = $this->json('userMessage');
	}
}