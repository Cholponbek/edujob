<?php

namespace App\Services\Billing;

use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function __construct(private PaymentGatewayInterface $gateway) {}

    /**
     * Создаёт подписку (pending) и счёт на неё, инициирует оплату через
     * текущий провайдер. Подписка становится active только после
     * markInvoicePaid() — то есть после реального подтверждения оплаты.
     *
     * @return array{subscription: Subscription, invoice: Invoice, payment: array}
     */
    public function subscribe(Institution $institution, Plan $plan): array
    {
        return DB::transaction(function () use ($institution, $plan) {
            $subscription = Subscription::create([
                'institution_id' => $institution->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
            ]);

            $invoice = Invoice::create([
                'institution_id' => $institution->id,
                'subscription_id' => $subscription->id,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'provider' => config('billing.driver'),
                'status' => 'pending',
            ]);

            $payment = $this->gateway->initiate($invoice);

            return compact('subscription', 'invoice', 'payment');
        });
    }

    /**
     * Отмечает счёт оплаченным и продлевает/активирует подписку. Продление
     * считается от текущего ends_at, если подписка ещё активна (досрочная
     * оплата не теряет остаток периода), иначе от текущего момента.
     */
    public function markInvoicePaid(Invoice $invoice, ?User $admin = null): void
    {
        DB::transaction(function () use ($invoice, $admin) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'marked_paid_by' => $admin?->id,
            ]);

            $subscription = $invoice->subscription;
            $plan = $subscription->plan;

            $periodStart = $subscription->isActive() ? $subscription->ends_at : now();

            $subscription->update([
                'status' => 'active',
                'starts_at' => $subscription->starts_at ?? now(),
                'ends_at' => $periodStart->copy()->addDays($plan->billing_period_days),
            ]);
        });
    }
}
