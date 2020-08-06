<?php

namespace Zorb\TBCPayment\Facades;

use Zorb\TBCPayment\TBCPayment as TBCPaymentService;
use Illuminate\Support\Facades\Facade;

class TBCPayment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TBCPaymentService::class;
    }
}
