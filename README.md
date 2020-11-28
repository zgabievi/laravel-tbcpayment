[![laravel-tbcpayment](https://banners.beyondco.de/TBC%20Payment.jpeg?theme=light&packageName=zgabievi%2Flaravel-tbcpayment&pattern=topography&style=style_1&description=TBC+Payment+integration+for+Laravel&md=1&showWatermark=0&fontSize=100px&images=cash)](https://github.com/zgabievi/laravel-tbcpayment)

# TBC Payment integration for Laravel

[![Packagist](https://img.shields.io/packagist/v/zgabievi/laravel-tbcpayment.svg)](https://packagist.org/packages/zgabievi/laravel-tbcpayment)
[![Packagist](https://img.shields.io/packagist/dt/zgabievi/laravel-tbcpayment.svg)](https://packagist.org/packages/zgabievi/laravel-tbcpayment)
[![license](https://img.shields.io/github/license/zgabievi/laravel-tbcpayment.svg)](https://packagist.org/packages/zgabievi/laravel-tbcpayment)

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
    - [Generate transaction](#generate-transaction)
    - [Check status](#check-status)
    - [Close day](#close-day)
    - [Block amount](#block-amount)
    - [Process blocked amount](#process-blocked-amount)
    - [Reverse transaction](#reverse-transaction)
    - [Refund transaction](#refund-transaction)
    - [Start recurring](#start-recurring)
    - [Register card](#register-card)
    - [Execute recurring](#execute-recurring)
    - [Take a credit](#take-a-credit)
- [Additional Information](#additional-information)
- [Configuration](#configuration)
- [License](#license)

## Installation

To get started, you need to install package:

```shell script
composer require zgabievi/laravel-tbcpayment
```

If your Laravel version is older than **5.5**, then add this to your service providers in *config/app.php*:

```php
'providers' => [
    ...
    Zorb\TBCPayment\TBCPaymentServiceProvider::class,
    ...
];
```

You can publish config file using this command:

```shell script
php artisan vendor:publish --provider="Zorb\TBCPayment\TBCPaymentServiceProvider"
```

This command will copy config file for you.

## Usage

- [Generate transaction](#generate-transaction)
- [Check status](#check-status)
- [Close day](#close-day)
- [Block amount](#block-amount)
- [Process blocked amount](#process-blocked-amount)
- [Reverse transaction](#reverse-transaction)
- [Refund transaction](#refund-transaction)
- [Start recurring](#start-recurring)
- [Register card](#register-card)
- [Execute recurring](#execute-recurring)
- [Take a credit](#take-a-credit)

### Generate transaction

This is initial request to start payment process

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // amount in minor value
        $amount = 10200; // 102.00 GEL
        
        // description of payment
        $description = 'Payment for charity';

        // language code
        $lang = 'KA'; // optional

        try {
            $result = TBCPayment::generate($amount, $description, $lang);

            if ($result->TRANSACTION_ID) {
                return TBCPayment::redirect($result->TRANSACTION_ID);
            } else {
                // transaction id was not generated
            }
        } catch (PaymentProcessException $exception) {
            // payment process failed
        }   
    }
}
```

### Check status

This method is used to check transaction status

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // transaction id provided by generate function
        $txn_id = 'XXXXXXXXXXXXXX';

        try {
            $result = TBCPayment::check($txn_id);

            // $result->RESULT: OK
            // $result->RESULT_CODE: 000
            // $result->RRN: 728418142503
            // $result->APPROVAL_CODE: 414576
            // $result->CARD_NUMBER: 4***********4813
            // $result->RECC_PMNT_ID: XXXXXX-XXXX-XXXX-XXXX-XXXXXX (recurring)
            // $result->RECC_PMNT_EXPIRY: 0822 (recurring)
        } catch (PaymentProcessException $exception) {
            // couldn't check transaction status
        }   
    }
}
```

### Close day

This method is used to close day and get all transactions on company's account

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        try {
            $result = TBCPayment::close();

            // $result->RESULT: OK
            // $result->RESULT_CODE: 500 (success)
            // $result->FLD_075: 12 (credits, reversal number)
            // $result->FLD_076: 31 (debits, number)
            // $result->FLD_087: 3201 (credits, reversal amount)
            // $result->FLD_088: 10099 (debits, amount)
        } catch (PaymentProcessException $exception) {
            // couldn't close day
        }   
    }
}
```

### Block amount

This method is used to block amount and process blocked amount later

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // amount in minor value
        $amount = 10200; // 102.00 GEL
        
        // description of payment
        $description = 'Payment for charity';

        // language code
        $lang = 'KA'; // optional

        try {
            $result = TBCPayment::preAuthorization($amount, $description, $lang);

            if ($result->TRANSACTION_ID) {
                // amount has been blocked
            } else {
                // transaction id was not generated
            }
        } catch (PaymentProcessException $exception) {
            // couldn't block amount
        }   
    }
}
```

### Process blocked amount

This method is used to commit blocked amount

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // transaction id provided by preAuthorization function
        $txn_id = 'XXXXXXXXXXXXXX';

        // amount in minor value
        $amount = 10200; // 102.00 GEL
        
        // description of payment
        $description = 'Payment for charity';

        try {
            $result = TBCPayment::authorization($txn_id, $amount, $description);

            // $result->RESULT: OK
            // $result->RESULT_CODE: 000
            // $result->RRN: 728418142503
            // $result->APPROVAL_CODE: 414576
            // $result->CARD_NUMBER: 4***********4813
        } catch (PaymentProcessException $exception) {
            // couldn't commit process
        }   
    }
}
```

### Reverse transaction

This method is used to reverse transaction **before** day is closed

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // transaction id generated by provider
        $txn_id = 'XXXXXXXXXXXXXX';

        try {
            $result = TBCPayment::reverse($txn_id);

            // $result->RESULT: OK
            // $result->RESULT_CODE: 400 (success)
        } catch (PaymentProcessException $exception) {
            // couldn't reverse transaction
        }   
    }
}
```

### Refund transaction

This method is used to reverse transaction **after** day is closed

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // transaction id generated by provider
        $txn_id = 'XXXXXXXXXXXXXX';

        try {
            $result = TBCPayment::refund($txn_id);

            // $result->RESULT: OK
            // $result->RESULT_CODE: 000 (success)
        } catch (PaymentProcessException $exception) {
            // couldn't refund transaction
        }   
    }
}
```

### Start recurring

This method is used to create subscription like payments

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // generate unique id for subscription
        $biller_id = 'XXXXXX-XXXX-XXXX-XXXX-XXXXXX';

        // amount in minor value
        $amount = 10200; // 102.00 GEL
        
        // description of payment
        $description = 'Payment for charity';

        // when should recurring end
        $expiration = '0422'; // optional (format: MMYY) 

        // language code
        $lang = 'KA'; // optional

        try {
            $result = TBCPayment::startSubscription($biller_id, $amount, $description, $expiration, $lang);

            if ($result->TRANSACTION_ID) {
                return TBCPayment::redirect($result->TRANSACTION_ID);
            } else {
                // transaction id was not generated
            }
        } catch (PaymentProcessException $exception) {
            // couldn't start recurring
        }   
    }
}
```

### Register card

This method is used to start subscription without charging instantly

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // generate unique id for subscription
        $biller_id = 'XXXXXX-XXXX-XXXX-XXXX-XXXXXX';
        
        // description of payment
        $description = 'Payment for charity';

        // when should recurring end
        $expiration = '0422'; // optional (format: MMYY) 

        // language code
        $lang = 'KA'; // optional

        try {
            $result = TBCPayment::registerSubscription($biller_id, $description, $expiration, $lang);

            if ($result->TRANSACTION_ID) {
                return TBCPayment::redirect($result->TRANSACTION_ID);
            } else {
                // transaction id was not generated
            }
        } catch (PaymentProcessException $exception) {
            // couldn't register card
        }   
    }
}
```

### Execute recurring

This method is used to repeat recurring process

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // generate unique id for subscription
        $biller_id = 'XXXXXX-XXXX-XXXX-XXXX-XXXXXX';

        // amount in minor value
        $amount = 10200; // 102.00 GEL
        
        // description of payment
        $description = 'Payment for charity';

        try {
            $result = TBCPayment::executeSubscription($biller_id, $amount, $description);

            // $result->RESULT: OK
            // $result->RESULT_CODE: 000
            // $result->RRN: 728418142503
            // $result->APPROVAL_CODE: 414576
            // $result->CARD_NUMBER: 4***********4813
        } catch (PaymentProcessException $exception) {
            // couldn't execute recurring
        }   
    }
}
```

### Take a credit

This method is used mainly by gambling providers

```php
use Zorb\TBCPayment\Exceptions\PaymentProcessException;
use Zorb\TBCPayment\Facades\TBCPayment;

class PaymentController extends Controller
{
    //
    public function __invoke()
    {
        // transaction id generated by bank
        $txn_id = 'XXXXXXXXXXXXXX';

        // amount in minor value
        $amount = 10200; // 102.00 GEL

        try {
            $result = TBCPayment::credit($txn_id, $amount);

            // $result->RESULT: OK
            // $result->RESULT_CODE: 000
            // $result->REFUND_TRANS_ID: XXXXXXXXXXX
        } catch (PaymentProcessException $exception) {
            // couldn't credit
        }   
    }
}
```

## Additional Information

### RESULT

| Value | Meaning |
| --- | --- |
| OK | Operation has been successfully completed |
| FAILED | Operation process has been failed |

### RESULT_CODE

Result codes has its own enum `Zorb\TBCPayment\Enums\ResultCode`

| Code | Key | Value |
| :---: | --- | --- |
| 000 | CODE_000 | Approved |
| 001 | CODE_001 | Approved, honour with identification |
| 002 | CODE_002 | Approved for partial amount |
| 003 | CODE_003 | Approved for VIP |
| 004 | CODE_004 | Approved, update track 3 |
| 005 | CODE_005 | Approved, account type specified by card issuer |
| 006 | CODE_006 | Approved for partial amount, account type specified by card issuer |
| 007 | CODE_007 | Approved, update ICC |
| 100 | CODE_100 | Decline (general, no comments) |
| 101 | CODE_101 | Decline, expired card |
| 102 | CODE_102 | Decline, suspected fraud |
| 103 | CODE_103 | Decline, card acceptor contact acquirer |
| 104 | CODE_104 | Decline, restricted card |
| 105 | CODE_105 | Decline, card acceptor call acquirer's security department |
| 106 | CODE_106 | Decline, allowable PIN tries exceeded |
| 107 | CODE_107 | Decline, refer to card issuer |
| 108 | CODE_108 | Decline, refer to card issuer\'s special conditions |
| 109 | CODE_109 | Decline, invalid merchant |
| 110 | CODE_110 | Decline, invalid amount |
| 111 | CODE_111 | Decline, invalid card number |
| 112 | CODE_112 | Decline, PIN data required |
| 113 | CODE_113 | Decline, unacceptable fee |
| 114 | CODE_114 | Decline, no account of type requested |
| 115 | CODE_115 | Decline, requested function not supported |
| 116 | CODE_116 | Decline, not sufficient funds |
| 117 | CODE_117 | Decline, incorrect PIN |
| 118 | CODE_118 | Decline, no card record |
| 119 | CODE_119 | Decline, transaction not permitted to cardholder |
| 120 | CODE_120 | Decline, transaction not permitted to terminal |
| 121 | CODE_121 | Decline, exceeds withdrawal amount limit |
| 122 | CODE_122 | Decline, security violation |
| 123 | CODE_123 | Decline, exceeds withdrawal frequency limit |
| 124 | CODE_124 | Decline, violation of law |
| 125 | CODE_125 | Decline, card not effective |
| 126 | CODE_126 | Decline, invalid PIN block |
| 127 | CODE_127 | Decline, PIN length error |
| 128 | CODE_128 | Decline, PIN kay sync error |
| 129 | CODE_129 | Decline, suspected counterfeit card |
| 180 | CODE_180 | Decline, by cardholders wish |
| 200 | CODE_200 | Pick-up (general, no comments) |
| 201 | CODE_201 | Pick-up, expired card |
| 202 | CODE_202 | Pick-up, suspected fraud |
| 203 | CODE_203 | Pick-up, card acceptor contact card acquirer |
| 204 | CODE_204 | Pick-up, restricted card |
| 205 | CODE_205 | Pick-up, card acceptor call acquirer's security department |
| 206 | CODE_206 | Pick-up, allowable PIN tries exceeded |
| 207 | CODE_207 | Pick-up, special conditions |
| 208 | CODE_208 | Pick-up, lost card |
| 209 | CODE_209 | Pick-up, stolen card |
| 210 | CODE_210 | Pick-up, suspected counterfeit card |
| 300 | CODE_300 | Status message: file action successful |
| 301 | CODE_301 | Status message: file action not supported by receiver |
| 302 | CODE_302 | Status message: unable to locate record on file |
| 303 | CODE_303 | Status message: duplicate record, old record replaced |
| 304 | CODE_304 | Status message: file record field edit error |
| 305 | CODE_305 | Status message: file locked out |
| 306 | CODE_306 | Status message: file action not successful |
| 307 | CODE_307 | Status message: file data format error |
| 308 | CODE_308 | Status message: duplicate record, new record rejected |
| 309 | CODE_309 | Status message: unknown file |
| 400 | CODE_400 | Accepted (for reversal) |
| 499 | CODE_499 | Approved, no original message data |
| 500 | CODE_500 | Status message: reconciled, in balance |
| 501 | CODE_501 | Status message: reconciled, out of balance |
| 502 | CODE_502 | Status message: amount not reconciled, totals provided |
| 503 | CODE_503 | Status message: totals for reconciliation not available |
| 504 | CODE_504 | Status message: not reconciled, totals provided |
| 600 | CODE_600 | Accepted (for administrative info) |
| 601 | CODE_601 | Status message: impossible to trace back original transaction |
| 602 | CODE_602 | Status message: invalid transaction reference number |
| 603 | CODE_603 | Status message: reference number/PAN incompatible |
| 604 | CODE_604 | Status message: POS photograph is not available |
| 605 | CODE_605 | Status message: requested item supplied |
| 606 | CODE_606 | Status message: request cannot be fulfilled - required documentation is not available |
| 680 | CODE_680 | List ready |
| 681 | CODE_681 | List not ready |
| 700 | CODE_700 | Accepted (List ready) |
| 800 | CODE_800 | Accepted (for network management) |
| 900 | CODE_900 | Advice acknowledged, no financial liability accepted |
| 901 | CODE_901 | Advice acknowledged, finansial liability accepted |
| 902 | CODE_902 | Decline reason message: invalid transaction |
| 903 | CODE_903 | Status message: re-enter transaction |
| 904 | CODE_904 | Decline reason message: format error |
| 905 | CODE_905 | Decline reason message: acquirer not supported by switch |
| 906 | CODE_906 | Decline reason message: cutover in process |
| 907 | CODE_907 | Decline reason message: card issuer or switch inoperative |
| 908 | CODE_908 | Decline reason message: transaction destination cannot be found for routing |
| 909 | CODE_909 | Decline reason message: system malfunction |
| 910 | CODE_910 | Decline reason message: card issuer signed off |
| 911 | CODE_911 | Decline reason message: card issuer timed out |
| 912 | CODE_912 | Decline reason message: card issuer unavailable |
| 913 | CODE_913 | Decline reason message: duplicate transmission |
| 914 | CODE_914 | Decline reason message: not able to trace back to original transaction |
| 915 | CODE_915 | Decline reason message: reconciliation cutover or checkpoint error |
| 916 | CODE_916 | Decline reason message: MAC incorrect |
| 917 | CODE_917 | Decline reason message: MAC key sync error |
| 918 | CODE_918 | Decline reason message: no communication keys available for use |
| 919 | CODE_919 | Decline reason message: encryption key sync error |
| 920 | CODE_920 | Decline reason message: security software/hardware error - try again |
| 921 | CODE_921 | Decline reason message: security software/hardware error - no action |
| 922 | CODE_922 | Decline reason message: message number out of sequence |
| 923 | CODE_923 | Status message: request in progress |
| 950 | CODE_950 | Decline reason message: violation of business arrangement |
| XXX | CODE_XXX | Unknown |

## Configuration

| Key | Meaning | Type | Default |
| --- | --- | :---: | --- |
| TBC_PAYMENT_DEBUG | This value decides to log or not to log requests | bool | false |
| TBC_PAYMENT_URL | Payment url provided by bank | string | https://ecommerce.ufc.ge:18443/ecomm2/MerchantHandler |
| TBC_PAYMENT_CERT_PATH | Certificate path in storage folder | string | app/tbc.pem |
| TBC_PAYMENT_CERT_PASSWORD | Certificate password provided by bank | string |  |
| TBC_PAYMENT_CURRENCY | Default currency for tbc payment | int | 981 |
| TBC_PAYMENT_LANGUAGE | Default language for tbc payment | string | EN |

## License

[zgabievi/laravel-tbcpayment](https://github.com/zgabievi/laravel-tbcpayment) is licensed under a [MIT License](https://github.com/zgabievi/laravel-tbcpayment/blob/master/LICENSE).
