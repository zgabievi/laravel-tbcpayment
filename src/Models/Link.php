<?php

namespace Zorb\TBCPayment\Models;

class Link
{
	/**
	 * URL.
	 *
	 * @var string
	 */
	public string $uri;

	/**
	 * Method to use on URL.
	 *
	 * @var string
	 */
	public string $method;

	/**
	 * Action to use on URL.
	 *
	 * @var string
	 */
	public string $rel;

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->uri = $data['uri'] ?? null;
		$this->method = $data['method'] ?? null;
		$this->rel = $data['rel'] ?? null;
	}
}