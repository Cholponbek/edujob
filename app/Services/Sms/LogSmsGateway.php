<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Драйвер по умолчанию для dev/CI — пишет сообщение в лог вместо реальной
 * отправки. Реальный шлюз (когда определится провайдер под рынок КР)
 * подключается отдельным классом, реализующим тот же контракт SmsGateway.
 */
class LogSmsGateway implements SmsGateway
{
    public function send(string $phone, string $message): void
    {
        Log::info("[SMS to {$phone}] {$message}");
    }
}
