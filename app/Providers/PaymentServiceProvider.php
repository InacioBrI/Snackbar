<?php

namespace App\Providers;

use App\Services\Payments\MercadoPagoGateway;
use App\Services\Payments\MockPaymentGateway;
use App\Services\Payments\PaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentGateway::class, function () {
            return match (config('payments.driver')) {
                'mercadopago' => new MercadoPagoGateway(config('payments.mercadopago.access_token')),
                default => new MockPaymentGateway,
            };
        });
    }
}
