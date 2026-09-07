<?php

return [

    // 'log' пишет код в лог вместо реальной отправки — для dev/CI.
    // Реальный шлюз подключается сменой драйвера, без изменений в
    // App\Services\Sms\SmsGateway (ARCHITECTURE.md §2, принцип #4 —
    // провайдер за интерфейсом).
    'driver' => env('SMS_GATEWAY_DRIVER', 'log'),

    'gateway_url' => env('SMS_GATEWAY_URL'),
    'gateway_api_key' => env('SMS_GATEWAY_API_KEY'),

    'otp_rate_limit_per_day' => (int) env('SMS_OTP_RATE_LIMIT_PER_DAY', 5),
    'otp_ttl_minutes' => 5,
    'otp_max_attempts' => 5,

    // Номера, для которых код всегда фиксированный (см. TEST_PHONE_NUMBERS
    // в ARCHITECTURE.md) — не шлём реальную SMS, чтобы можно было
    // тестировать флоу без живого шлюза.
    'test_phone_numbers' => array_filter(array_map(
        'trim',
        explode(',', (string) env('TEST_PHONE_NUMBERS', ''))
    )),
    'test_otp_code' => '000000',
];
