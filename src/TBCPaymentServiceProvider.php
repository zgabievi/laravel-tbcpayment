<?php

namespace Zorb\TBCPayment;

use Illuminate\Support\ServiceProvider;

class TBCPaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/config/tbcpayment.php' => config_path('tbcpayment.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/config/tbcpayment.php', 'tbcpayment');

        $this->loadViewsFrom(__DIR__ . '/views', 'tbcpayment');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(TBCPayment::class);
    }
}
