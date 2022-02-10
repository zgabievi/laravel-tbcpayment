<?php

namespace Zorb\TBCPayment;

use Zorb\TBCPayment\Responses\CompletePreAuthorizedPaymentResponse;
use Zorb\TBCPayment\Responses\ExecuteRecurringPaymentResponse;
use Zorb\TBCPayment\Responses\DeleteRecurringPaymentResponse;
use Zorb\TBCPayment\Responses\GetAccessTokenResponse;
use Zorb\TBCPayment\Responses\GetPaymentDetailsResponse;
use Zorb\TBCPayment\Responses\CreatePaymentResponse;
use Zorb\TBCPayment\Responses\CancelPaymentResponse;
use Zorb\TBCPayment\Responses\ErrorResponse;
use Zorb\TBCPayment\Models\RecurringPayment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Zorb\TBCPayment\Models\Payment;
use Carbon\Carbon;
use Exception;

class TBCPayment
{
	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-get-checkout-access-token
	 * @return GetAccessTokenResponse|ErrorResponse
	 */
	public function getAccessToken(): GetAccessTokenResponse | ErrorResponse
	{
		$response = Http::tpay()->asForm()
			->post('access-token', [
				'client_id' => Config::get('tbcpayment.client_id'),
				'client_secret' => Config::get('tbcpayment.client_secret'),
			]);

		if ($response->ok()) {
			return new GetAccessTokenResponse($response);
		}

		return new ErrorResponse($response);
	}

	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-create-checkout-payment
	 * @param Payment $payment
	 * @return CreatePaymentResponse|ErrorResponse
	 * @throws Exception
	 */
	public function createPayment(Payment $payment): CreatePaymentResponse | ErrorResponse
	{
		$response = Http::tpay()->withToken($this->accessToken())->post('payments', $payment->toArray());

		if ($response->ok()) {
			return new CreatePaymentResponse($response);
		}

		return new ErrorResponse($response);
	}

	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-get-checkout-payment-details
	 * @param string $payId
	 * @return GetPaymentDetailsResponse|ErrorResponse
	 * @throws Exception
	 */
	public function getPaymentDetails(string $payId): GetPaymentDetailsResponse | ErrorResponse
	{
		$response = Http::tpay()->withToken($this->accessToken())->get("payments/{$payId}");

		if ($response->ok()) {
			return new GetPaymentDetailsResponse($response);
		}

		return new ErrorResponse($response);
	}

	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-cancel-checkout-payment
	 * @param string $payId
	 * @param float $amount
	 * @return CancelPaymentResponse|ErrorResponse
	 * @throws Exception
	 */
	public function cancelPayment(string $payId, float $amount): CancelPaymentResponse | ErrorResponse
	{
		$response = Http::tpay()->withToken($this->accessToken())->post("payments/{$payId}/cancel", compact('amount'));

		if ($response->ok()) {
			return new CancelPaymentResponse($response);
		}

		return new ErrorResponse($response);
	}

	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-complete-pre-authorized-payment
	 * @param string $payId
	 * @param float $amount
	 * @return CompletePreAuthorizedPaymentResponse|ErrorResponse
	 * @throws Exception
	 */
	public function completePreAuthorizedPayment(string $payId, float $amount): CompletePreAuthorizedPaymentResponse | ErrorResponse
	{
		$response = Http::tpay()->withToken($this->accessToken())->post("payments/{$payId}/completion", compact('amount'));

		if ($response->ok()) {
			return new CompletePreAuthorizedPaymentResponse($response);
		}

		return new ErrorResponse($response);
	}

	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-execute-recurring-payment
	 * @param RecurringPayment $payment
	 * @return ExecuteRecurringPaymentResponse|ErrorResponse
	 * @throws Exception
	 */
	public function executeRecurringPayment(RecurringPayment $payment): ExecuteRecurringPaymentResponse | ErrorResponse
	{
		$response = Http::tpay()->withToken($this->accessToken())->post('payments/execution', $payment->toArray());

		if ($response->ok()) {
			return new ExecuteRecurringPaymentResponse($response);
		}

		return new ErrorResponse($response);
	}

	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-delete-recurring-payment
	 * @param string $recId
	 * @return DeleteRecurringPaymentResponse|ErrorResponse
	 * @throws Exception
	 */
	public function deleteRecurringPayment(string $recId): DeleteRecurringPaymentResponse | ErrorResponse
	{
		$response = Http::tpay()->withToken($this->accessToken())->post("payments/{$recId}/delete");

		if ($response->ok()) {
			return new DeleteRecurringPaymentResponse($response);
		}

		return new ErrorResponse($response);
	}

	/**
	 * @see https://developers.tbcbank.ge/docs/checkout-get-checkout-access-token
	 * @return string
	 * @throws Exception
	 */
	protected function accessToken(): string
	{
		$token_ttl = Config::get('tbcpayment.token_ttl', 1440);

		$result = Cache::remember('laravel-tbcpayment.access_token', Carbon::now()->addMinutes($token_ttl), fn() => $this->getAccessToken());

		if (!$result?->accessToken) {
			throw new Exception('You need to generate access token first.');
		}

		return $result->accessToken;
	}
}
