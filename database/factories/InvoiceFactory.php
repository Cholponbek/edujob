<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'subscription_id' => Subscription::factory(),
            'amount' => fake()->randomElement([990, 2900, 5900]),
            'currency' => 'KGS',
            'provider' => 'manual',
            'status' => 'pending',
        ];
    }
}
