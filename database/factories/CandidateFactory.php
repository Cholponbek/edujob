<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject' => fake()->randomElement(['Математика', 'Русский язык', 'Кыргызский язык', 'Физика', 'Биология', 'Английский язык']),
            'education_levels' => fake()->randomElements(['preschool', 'primary', 'secondary', 'vocational', 'higher'], 2),
            'diploma_document_path' => 'documents/diplomas/example.pdf',
            'teaching_category' => fake()->randomElement(['высшая', 'первая', 'вторая', null]),
            'relocation_ready' => fake()->boolean(30),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'either']),
            'desired_stake_fraction' => fake()->randomElement([0.5, 0.75, 1.0]),
        ];
    }
}
