<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' школа',
            'type' => fake()->randomElement(['kindergarten', 'school', 'vocational', 'university']),
            'region' => fake()->randomElement(['Бишкек', 'Ош', 'Чуйская область', 'Иссык-Кульская область', 'Джалал-Абадская область']),
            'district' => fake()->city(),
            'verification_status' => 'pending',
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }
}
