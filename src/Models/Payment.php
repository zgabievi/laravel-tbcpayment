<?php

namespace Zorb\TBCPayment\Models;

use Illuminate\Support\Facades\Log;
use Zorb\TBCPayment\Enums\Language;
use Zorb\TBCPayment\Enums\PaymentMethod;

class Payment
{
	/**
	 * Transaction amount.
	 *
	 * @var Amount
	 */
	protected Amount $_amount;

	/**
	 * Callback url to redirect user after finishing payment.
	 *
	 * @var string
	 */
	protected string $_returnUrl;

	/**
	 * Additional parameter for merchant specific info (optional).
	 * Only non-unicode (ANS) symbols allowed (max length 25).
	 * This parameter will appear in the account statement.
	 *
	 * @var ?string
	 */
	protected ?string $_extra;

	/**
	 * Additional parameter for merchant specific info (optional).
	 * Only non-unicode (ANS) symbols allowed (max length 52).
	 *
	 * @var ?string
	 */
	protected ?string $_extra2;

	/**
	 * Payment initiation expiration time in minutes.
	 *
	 * @var ?int
	 */
	protected ?int $_expirationMinutes;

	/**
	 * There are several payment methods available to be passed when initiating TBC E-Commerce payment.
	 *
	 * @var ?array<PaymentMethod>
	 */
	protected ?array $_methods;

	/**
	 * List of installment products. mandatory if installment is selected as payment method.
	 * Sum of prices of installment products should be same as total amount.
	 *
	 * @var ?array<InstallmentProduct>
	 */
	protected ?array $_installmentProducts;

	/**
	 * When payment status changes to final status, POST request containing PaymentId in the body will be sent to given URL.
	 *
	 * @var ?string
	 */
	protected ?string $_callbackUrl;

	/**
	 * Specify if pre-authorization is needed for the transaction.
	 * If "true" is passed, amount will be blocked on the card and additional request should be executed by merchant to complete payment.
	 *
	 * @var ?bool
	 */
	protected ?bool $_preAuth;

	/**
	 * Default language for payment page.
	 *
	 * @var ?Language
	 */
	protected ?Language $_language;

	/**
	 * Merchant-side payment identifier.
	 *
	 * @var ?string
	 */
	protected ?string $_merchantPaymentId;

	/**
	 * If true is passed, TBC E-Commerce info message will be skipped and customer will be redirected to merchant.
	 * If false is passed or this parameter isn’t passed at all, TBC E-Commerce info message will be shown and customer will be redirected to merchant.
	 *
	 * @var ?bool
	 */
	protected ?bool $_skipInfoMessage;

	/**
	 * Specify if saving card function is needed.
	 * This function should be enabled for the merchant by bank.
	 * If true is passed, recId parameter should be returned in response, through this parameter merchant can execute payment by saved card - POST /payments/execution.
	 * Zero amount is allowed for this function.
	 * If card saving function is requested with pre-authorization parameter=true, saved card execution method will be activated after pre-authorization completion.
	 * WebQR, ApplePay and installments pay methods are not allowed for saving card request.
	 *
	 * @var ?bool
	 */
	protected ?bool $_saveCard;

	/**
	 * The date until the card will be saved can be passed in following format "MMYY".
	 * If the saveCardToDate is not provided or data provided by the merchant exceeds card expiry, the system will automatically assign the SaveCardToDate value that will be equal to card expiry.
	 * The actual card save date must be verified by getting payment status after transaction completed with GET /payments/{payment-id}.
	 *
	 * @var ?string
	 */
	protected ?string $_saveCardToDate;

	/**
	 * Payment short description for clients, max length 30.
	 * This parameter will appear on the checkout page.
	 *
	 * @var ?string
	 */
	protected ?string $_description;

	/**
	 * @return static
	 */
	public static function make(): static
	{
		return new static();
	}

	/**
	 * @param Amount $amount
	 * @return $this
	 */
	public function amount(Amount $amount): static
	{
		$this->_amount = $amount;
		return $this;
	}

	/**
	 * @param string $returnUrl
	 * @return $this
	 */
	public function price(string $returnUrl): static
	{
		$this->_returnUrl = $returnUrl;
		return $this;
	}

	/**
	 * @param string $extra
	 * @return $this
	 */
	public function extra(string $extra): static
	{
		$this->_extra = $extra;
		return $this;
	}

	/**
	 * @param string $extra2
	 * @return $this
	 */
	public function extra2(string $extra2): static
	{
		$this->_extra2 = $extra2;
		return $this;
	}

	/**
	 * @param int $expirationMinutes
	 * @return $this
	 */
	public function expirationMinutes(int $expirationMinutes): static
	{
		$this->_expirationMinutes = $expirationMinutes;
		return $this;
	}

	/**
	 * @param array<PaymentMethod> $methods
	 * @return $this
	 */
	public function methods(array $methods): static
	{
		$this->_methods = $methods;
		return $this;
	}

	/**
	 * @param array<InstallmentProduct> $installmentProducts
	 * @return $this
	 */
	public function installmentProducts(array $installmentProducts): static
	{
		$this->_installmentProducts = $installmentProducts;
		return $this;
	}

	/**
	 * @param string $callbackUrl
	 * @return $this
	 */
	public function callbackUrl(string $callbackUrl): static
	{
		$this->_callbackUrl = $callbackUrl;
		return $this;
	}

	/**
	 * @param bool $preAuth
	 * @return $this
	 */
	public function preAuth(bool $preAuth): static
	{
		$this->_preAuth = $preAuth;
		return $this;
	}

	/**
	 * @param Language $language
	 * @return $this
	 */
	public function language(Language $language): static
	{
		$this->_language = $language;
		return $this;
	}

	/**
	 * @param string $merchantPaymentId
	 * @return $this
	 */
	public function merchantPaymentId(string $merchantPaymentId): static
	{
		$this->_merchantPaymentId = $merchantPaymentId;
		return $this;
	}

	/**
	 * @param bool $skipInfoMessage
	 * @return $this
	 */
	public function skipInfoMessage(bool $skipInfoMessage): static
	{
		$this->_skipInfoMessage = $skipInfoMessage;
		return $this;
	}

	/**
	 * @param bool $saveCard
	 * @return $this
	 */
	public function saveCard(bool $saveCard): static
	{
		$this->_saveCard = $saveCard;
		return $this;
	}

	/**
	 * @param string $saveCardToDate
	 * @return $this
	 */
	public function saveCardToDate(string $saveCardToDate): static
	{
		$this->_saveCardToDate = $saveCardToDate;
		return $this;
	}

	/**
	 * @param string $description
	 * @return $this
	 */
	public function description(string $description): static
	{
		$this->_description = $description;
		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$result = [
			'amount' => $this->_amount->toArray(),
			'returnurl' => $this->_returnUrl,
			'extra' => $this->_extra,
			'extra2' => $this->_extra2,
			'expirationMinutes' => $this->_expirationMinutes,
			'methods' => $this->_methods,
			'installmentProducts' => $this->_installmentProducts,
			'callbackUrl' => $this->_callbackUrl,
			'preAuth' => $this->_preAuth,
			'language' => $this->_language,
			'merchantPaymentId' => $this->_merchantPaymentId,
			'skipInfoMessage' => $this->_skipInfoMessage,
			'saveCard' => $this->_saveCard,
			'saveCardToDate' => $this->_saveCardToDate,
			'description' => $this->_description,
		];

		if (config('tbcpayment.debug')) {
			Log::debug('Payment@toArray', $result);
		}

		return array_filter($result, fn($key, $value) => !is_null($value), ARRAY_FILTER_USE_BOTH);
	}
}