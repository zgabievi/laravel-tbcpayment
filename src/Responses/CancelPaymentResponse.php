<?php

namespace Zorb\TBCPayment\Responses;

use Illuminate\Http\Client\Response;

class CancelPaymentResponse extends Response
{
	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response->toPsrResponse());
	}
}