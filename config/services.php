<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // ИИ-скрининг, парсинг резюме, двуязычная генерация, рекомендации
    // (ARCHITECTURE.md §2) — все вызовы асинхронные, через очередь.
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-opus-5'),
        // $/1M токенов — для оценки стоимости в ai_usage_logs. Обновлять
        // при смене модели через ANTHROPIC_MODEL.
        'input_price_per_million' => (float) env('ANTHROPIC_INPUT_PRICE_PER_MILLION', 5.00),
        'output_price_per_million' => (float) env('ANTHROPIC_OUTPUT_PRICE_PER_MILLION', 25.00),
    ],

];
