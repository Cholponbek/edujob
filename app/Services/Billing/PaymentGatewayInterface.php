<?php

namespace App\Services\Billing;

use App\Models\Invoice;

/**
 * Провайдер оплаты за интерфейсом (ARCHITECTURE.md §3, принцип #4) —
 * ручной инвойс сейчас, мбанк/Элсом подключаются позже без переписывания
 * домена (BillingService, модели Subscription/Invoice не меняются).
 */
interface PaymentGatewayInterface
{
    /**
     * Инициирует оплату счёта. Возвращает то, что нужно показать
     * учреждению: инструкции по оплате (ручной инвойс) или redirect_url
     * на форму оплаты провайдера (онлайн-эквайринг).
     *
     * @return array{instructions?: string, redirect_url?: string}
     */
    public function initiate(Invoice $invoice): array;
}
