<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Старт', 'Бизнес', 'Про']),
            'price' => fake()->randomElement([990, 2900, 5900]),
            'currency' => 'KGS',
            'billing_period_days' => 30,
            'is_active' => true,
        ];
    }
}
