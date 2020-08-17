<?php

namespace Zorb\TBCPayment;

use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TBCPayment
{
    /**
     * Post fields for curl request
     *
     * @var array
     */
    protected $post_fields;

    /**
     * Generate transaction id
     *
     * @param int $amount
     * @param string $description
     * @param string|null $lang
     * @return object
     * @throws PaymentProcessException
     */
    function generate(int $amount, string $description, string $lang = null): object
    {
        $client_ip = request()->ip();
        $language = $this->language($lang);
        $currency = config('payment.currency');

        $this->post_fields = "command=v&amount={$amount}&currency={$currency}&client_ip_addr={$client_ip}&description={$description}&language={$language}&msg_type=SMS";
        return $this->parse($this->process());
    }

    /**
     * Redirect to payment process
     *
     * @param string $txn_id
     * @return View
     */
    function redirect(string $txn_id): View
    {
        return view('tbcpayment::redirect', compact('txn_id'));
    }

    /**
     * Check transaction status
     *
     * @param string|null $txn_id
     * @return object
     * @throws PaymentProcessException
     */
    function check(string $txn_id = null): object
    {
        $client_ip = request()->ip();
        $trans_id = urlencode($txn_id ?: request()->input('trans_id'));

        $this->post_fields = "command=c&trans_id={$trans_id}&client_ip_addr={$client_ip}";
        return $this->parse($this->process());
    }

    /**
     * Close day to get money on your account
     *
     * @return object
     * @throws PaymentProcessException
     */
    function close(): object
    {
        $this->post_fields = 'command=b';
        return $this->parse($this->process());
    }

    /**
     * Block amount of customer
     *
     * @param int $amount
     * @param string $description
     * @param string|null $lang
     * @return object
     * @throws PaymentProcessException
     */
    function preAuthorization(int $amount, string $description, string $lang = null): object
    {
        $client_ip = request()->ip();
        $language = $this->language($lang);
        $currency = config('payment.currency');

        $this->post_fields = "command=a&amount={$amount}&currency={$currency}&client_ip_addr={$client_ip}&description={$description}&language={$language}&msg_type=DMS";
        return $this->parse($this->process());
    }

    /**
     * Use after pre-authorization to commit blocked amount
     *
     * @param string $txn_id
     * @param int $amount
     * @param string $description
     * @return object
     * @throws PaymentProcessException
     */
    function authorization(string $txn_id, int $amount, string $description): object
    {
        $client_ip = request()->ip();
        $currency = config('payment.currency');

        $this->post_fields = "command=t&trans_id={$txn_id}&currency={$currency}&amount={$amount}&client_ip_addr={$client_ip}&description={$description}&msg_type=DMS";
        return $this->parse($this->process());
    }

    /**
     * Refund before day is closed
     *
     * @param string $txn_id
     * @return object
     * @throws PaymentProcessException
     */
    function reverse(string $txn_id): object
    {
        $txn_id = urlencode($txn_id);

        $this->post_fields = "command=r&trans_id={$txn_id}";
        return $this->parse($this->process());
    }

    /**
     * Refund after day is closed
     *
     * @param string $txn_id
     * @return object
     * @throws PaymentProcessException
     */
    function refund(string $txn_id): object
    {
        $txn_id = urlencode($txn_id);

        $this->post_fields = "command=k&trans_id={$txn_id}";
        return $this->parse($this->process());
    }

    /**
     * Start recurring subscription
     *
     * @param string $biller_id
     * @param int $amount
     * @param string $description
     * @param string|null $expiration
     * @param string|null $lang
     * @return object
     * @throws PaymentProcessException
     */
    function startSubscription(string $biller_id, int $amount, string $description, string $expiration = null, string $lang = null): object
    {
        $client_ip = request()->ip();
        $language = $this->language($lang);
        $currency = config('payment.currency');
        $expiry = $expiration ?: now()->addYear()->format('my');

        $this->post_fields = "command=z&amount={$amount}&currency={$currency}&client_ip_addr={$client_ip}&description={$description}&language={$language}&msg_type=SMS&biller_client_id={$biller_id}&perspayee_expiry={$expiry}&perspayee_gen=1";
        return $this->parse($this->process());
    }

    /**
     * Register recurring subscription without committing money
     *
     * @param string $biller_id
     * @param string $description
     * @param string|null $expiration
     * @param string|null $lang
     * @return object
     * @throws PaymentProcessException
     */
    function registerSubscription(string $biller_id, string $description, string $expiration = null, string $lang = null): object
    {
        $client_ip = request()->ip();
        $language = $this->language($lang);
        $currency = config('payment.currency');
        $expiry = $expiration ?: now()->addYear()->format('my');

        $this->post_fields = "command=p&amount=0&currency={$currency}&client_ip_addr={$client_ip}&description={$description}&language={$language}&msg_type=AUTH&biller_client_id={$biller_id}&perspayee_expiry={$expiry}&perspayee_gen=1";
        return $this->parse($this->process());
    }

    /**
     * Execute already registered subscription to be repeated
     *
     * @param string $biller_id
     * @param int $amount
     * @param string $description
     * @return object
     * @throws PaymentProcessException
     */
    function executeSubscription(string $biller_id, int $amount, string $description): object
    {
        $client_ip = request()->ip();
        $currency = config('payment.currency');

        $this->post_fields = "command=e&amount={$amount}&currency={$currency}&client_ip_addr={$client_ip}&description={$description}&biller_client_id={$biller_id}";
        return $this->parse($this->process());
    }

    /**
     * Get credit from the bank
     *
     * @param string $txn_id
     * @param int $amount
     * @return object
     * @throws PaymentProcessException
     */
    function credit(string $txn_id, int $amount): object
    {
        $this->post_fields = "command=g&trans_id={$txn_id}&amount={$amount}";
        return $this->parse($this->process());
    }

    /**
     * Get language for each process
     *
     * @param string|null $lang
     * @return string
     */
    protected function language(string $lang = null): string
    {
        $language = $lang ? strtoupper($lang) : config('payment.language');
        $language = $language === 'KA' ? 'GE' : $language;
        $language = in_array($language, ['GE', 'EN']) ? $language : config('payment.language');

        return $language;
    }

    /**
     * cURL process for each method
     *
     * @return string
     * @throws PaymentProcessException
     */
    protected function process(): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSLVERSION, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->post_fields);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_SSLCERT, storage_path(config('tbcpayment.cert_path')));
        curl_setopt($curl, CURLOPT_SSLKEYPASSWD, config('tbcpayment.password'));
        curl_setopt($curl, CURLOPT_URL, config('tbcpayment.url'));
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        if (config('payment.debug')) {
            Log::debug($result);
            Log::debug($info);
        }

        if (curl_errno($curl)) {
            throw new PaymentProcessException(curl_error($curl));
        }

        curl_close($curl);

        return $result;
    }

    /**
     * Parse result from bank
     *
     * @param $data
     * @return object
     */
    protected function parse($data): object
    {
        $params = explode(PHP_EOL, trim($data));
        $result = [];

        foreach ($params as $param) {
            $parts = explode(':', $param);
            $result[$parts[0]] = trim($parts[1]);
        }

        return (object)$result;
    }
}
