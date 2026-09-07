<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
