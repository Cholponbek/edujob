<?php

namespace App\Providers;

use App\Services\Billing\ManualInvoiceGateway;
use App\Services\Billing\PaymentGatewayInterface;
use App\Services\Sms\LogSmsGateway;
use App\Services\Sms\NikitaSmsGateway;
use App\Services\Sms\SmsGateway;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsGateway::class, match (config('sms.driver')) {
            'nikita' => NikitaSmsGateway::class,
            // Следующий провайдер подключается сюда дополнительной
            // match-веткой, без изменений в вызывающем коде
            // (ARCHITECTURE.md §3, принцип #4).
            default => LogSmsGateway::class,
        });

        $this->app->bind(PaymentGatewayInterface::class, match (config('billing.driver')) {
            // mbank/elsom подключаются сюда так же, отдельной веткой.
            default => ManualInvoiceGateway::class,
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
