<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\StaffRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'staff_request_id' => StaffRequest::factory(),
            'candidate_id' => Candidate::factory(),
            'status' => 'applied',
        ];
    }
}
