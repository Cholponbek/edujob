<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'plan_id' => Plan::factory(),
            'status' => 'pending',
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);
    }
}
