<?php

return [

    // 'manual' — оплата по реквизитам, подтверждается администратором
    // вручную в Filament. 'mbank'/'elsom' подключаются позже отдельными
    // классами за PaymentGatewayInterface (ARCHITECTURE.md §3, принцип #4).
    'driver' => env('BILLING_GATEWAY_DRIVER', 'manual'),

    'bank_requisites' => env(
        'BILLING_BANK_REQUISITES',
        'Реквизиты для оплаты уточняются — свяжитесь с менеджером EduJob.'
    ),

];
