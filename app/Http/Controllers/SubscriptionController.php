<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\Billing\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function store(Request $request, BillingService $billing): RedirectResponse
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $institution = $request->user()->institutions()->first();
        abort_unless($institution !== null, 403, 'Только сотрудник учреждения может оформить подписку.');

        $plan = Plan::where('is_active', true)->findOrFail($data['plan_id']);

        $billing->subscribe($institution, $plan);

        return back()->with('status', 'subscription-pending');
    }
}
