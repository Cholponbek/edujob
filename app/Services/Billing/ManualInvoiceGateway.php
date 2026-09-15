<?php

namespace App\Services\Billing;

use App\Models\Invoice;

/**
 * Первый драйвер биллинга: без онлайн-эквайринга — учреждение платит по
 * реквизитам, администратор вручную отмечает счёт оплаченным в Filament
 * (InvoiceResource, действие "Отметить оплаченным").
 */
class ManualInvoiceGateway implements PaymentGatewayInterface
{
    public function initiate(Invoice $invoice): array
    {
        return [
            'instructions' => sprintf(
                "Счёт №%d на сумму %d %s.\n%s\nПосле оплаты счёт будет подтверждён администратором EduJob.",
                $invoice->id,
                $invoice->amount,
                $invoice->currency,
                config('billing.bank_requisites'),
            ),
        ];
    }
}
