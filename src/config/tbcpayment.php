<?php

return [
    /**
     * This value decides to log or not to log requests.
     */
    'debug' => env('TBC_PAYMENT_DEBUG', false),

	/**
	 * API Url for tbc payment operations.
	 */
	'api_url' => env('TBC_PAYMENT_API_URL', 'https://api.tbcbank.ge/v1/tpay/'),

	/**
	 * API Key from TBC developers page.
	 */
	'api_key' => env('TBC_PAYMENT_API_KEY'),

    /**
     * Minutes to live for access token cache.
     */
    'token_ttl' => env('TBC_PAYMENT_TOKEN_TTL', 1440),

    /**
     * Client ID from TBC developers page.
     */
    'client_id' => env('TBC_PAYMENT_CLIENT_ID'),

    /**
     * Client secret from TBC developers page.
     */
    'client_secret' => env('TBC_PAYMENT_CLIENT_SECRET'),
];
