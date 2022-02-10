<?php

namespace Zorb\TBCPayment;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

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
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app->bind(TBCPayment::class);

		Http::macro('tpay', fn() => Http::withHeaders(['apiKey' => Config::get('tbcpayment.api_key')])
			->baseUrl(Config::get('tbcpayment.api_url'))
		);
	}
}
