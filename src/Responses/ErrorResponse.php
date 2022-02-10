<?php

namespace Zorb\TBCPayment\Responses;

use Illuminate\Http\Client\Response;
use Zorb\TBCPayment\Enums\ResultCode;

class ErrorResponse extends Response
{
	/**
	 * A URI identifying a human-readable web page with information about the error.
	 *
	 * @var string
	 */
	public string $type;

	/**
	 * General error description.
	 *
	 * @var string
	 */
	public string $title;

	/**
	 * Http status code.
	 *
	 * @var string
	 */
	public string $status;

	/**
	 * Error code in following format: {method-name}.{http-status-code}
	 *
	 * @var string
	 */
	public string $systemCode;

	/**
	 * Human-readable text providing additional information.
	 *
	 * @var string
	 */
	public string $detail;

	/**
	 * Business level transaction status description.
	 *
	 * @see https://developers.tbcbank.ge/docs/result-code
	 * @var ?ResultCode
	 */
	public ?ResultCode $resultCode;

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response->toPsrResponse());

		$this->type = $this->json('type');
		$this->title = $this->json('title');
		$this->status = $this->json('status');
		$this->systemCode = $this->json('systemCode');
		$this->detail = $this->json('detail');
		$this->resultCode = $this->json('resultCode');
	}
}