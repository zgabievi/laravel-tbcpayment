<?php

namespace Zorb\TBCPayment\Responses;

use Illuminate\Http\Client\Response;

class GetAccessTokenResponse extends Response
{
	/**
	 * The access token value.
	 *
	 * @var string
	 */
	public string $accessToken;

	/**
	 * Type of the token is set to "Bearer".
	 *
	 * @var string
	 */
	public string $tokenType;

	/**
	 * The lifetime in seconds of the access token.
	 *
	 * @var int
	 */
	public int $expiresIn;

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response->toPsrResponse());

		$this->accessToken = $this->json('access_token');
		$this->tokenType = $this->json('token_type');
		$this->expiresIn = $this->json('expires_in');
	}
}