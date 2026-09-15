<?php

use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Plan;
use App\Services\Billing\BillingService;

test('subscribing creates a pending subscription and invoice', function () {
    $institution = Institution::factory()->create();
    $plan = Plan::factory()->create(['price' => 2900, 'billing_period_days' => 30]);

    $result = app(BillingService::class)->subscribe($institution, $plan);

    expect($result['subscription']->status)->toBe('pending');
    expect($result['subscription']->institution_id)->toBe($institution->id);
    expect($result['invoice']->status)->toBe('pending');
    expect($result['invoice']->amount)->toBe(2900);
    expect($result['payment'])->toHaveKey('instructions');

    $this->assertDatabaseHas('subscriptions', [
        'institution_id' => $institution->id,
        'plan_id' => $plan->id,
        'status' => 'pending',
    ]);
});

test('marking an invoice paid activates the subscription for the plan period', function () {
    $institution = Institution::factory()->create();
    $plan = Plan::factory()->create(['billing_period_days' => 30]);

    $result = app(BillingService::class)->subscribe($institution, $plan);

    app(BillingService::class)->markInvoicePaid($result['invoice']);

    $result['invoice']->refresh();
    $result['subscription']->refresh();

    expect($result['invoice']->status)->toBe('paid');
    expect($result['invoice']->paid_at)->not->toBeNull();
    expect($result['subscription']->status)->toBe('active');
    expect($result['subscription']->ends_at->diffInDays(now()))->toBeLessThanOrEqual(30);
    expect($institution->hasActiveSubscription())->toBeTrue();
});

test('paying a renewal invoice extends from the current period end, not from now', function () {
    $institution = Institution::factory()->create();
    $plan = Plan::factory()->create(['billing_period_days' => 30]);

    $result = app(BillingService::class)->subscribe($institution, $plan);
    app(BillingService::class)->markInvoicePaid($result['invoice']);

    $firstEndsAt = $result['subscription']->fresh()->ends_at;

    // Счёт на продление той же подписки (следующий период), а не новая подписка.
    $renewalInvoice = Invoice::factory()->create([
        'institution_id' => $institution->id,
        'subscription_id' => $result['subscription']->id,
        'amount' => $plan->price,
    ]);

    app(BillingService::class)->markInvoicePaid($renewalInvoice);

    $result['subscription']->refresh();

    expect($result['subscription']->ends_at->equalTo($firstEndsAt->copy()->addDays(30)))->toBeTrue();
});
