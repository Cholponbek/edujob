<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\CandidateRecommendation;
use App\Models\StaffRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CandidateRecommendation>
 */
class CandidateRecommendationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'staff_request_id' => StaffRequest::factory(),
            'score' => fake()->numberBetween(0, 100),
            'reasoning' => fake()->sentence(),
            'generated_at' => now(),
        ];
    }
}
