<?php

return [

    // 'log' пишет код в лог вместо реальной отправки — для dev/CI.
    // 'nikita' — боевой шлюз smspro.nikita.kg (XML/HTTP-протокол).
    // Смена драйвера не требует изменений в App\Services\Sms\SmsGateway
    // (ARCHITECTURE.md §2, принцип #4 — провайдер за интерфейсом).
    'driver' => env('SMS_GATEWAY_DRIVER', 'log'),

    'nikita' => [
        'base_url' => env('NIKITA_SMS_BASE_URL', 'https://smspro.nikita.kg/api'),
        'login' => env('NIKITA_SMS_LOGIN'),
        'password' => env('NIKITA_SMS_PASSWORD'),
        // До 11 латинских букв/цифр/точки/тире, либо 14 цифр — не
        // валидировано автоматически, имя отправителя подтверждает
        // администратор smspro.nikita.kg вручную (см. XML-протокол §1,
        // код ошибки 5).
        'sender' => env('NIKITA_SMS_SENDER', 'EduJob'),
        // Если true — <test>1</test> в запросе: шлюз принимает и
        // валидирует запрос, но реально не отправляет и не тарифицирует.
        // По умолчанию включено везде, кроме production.
        'test_mode' => (bool) env('NIKITA_SMS_TEST_MODE', env('APP_ENV') !== 'production'),
    ],

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
