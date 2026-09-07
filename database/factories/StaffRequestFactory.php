<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\StaffRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffRequest>
 */
class StaffRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'created_by' => User::factory(),
            'title' => 'Учитель '.fake()->randomElement(['математики', 'русского языка', 'физики', 'английского языка']),
            'subject' => fake()->randomElement(['Математика', 'Русский язык', 'Физика', 'Английский язык']),
            'education_level' => fake()->randomElement(['preschool', 'primary', 'secondary', 'vocational', 'higher']),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'either']),
            'stake_fraction' => fake()->randomElement([0.5, 0.75, 1.0]),
            'is_next_school_year' => fake()->boolean(20),
            'status' => 'published',
            'published_at' => now(),
        ];
    }
}
