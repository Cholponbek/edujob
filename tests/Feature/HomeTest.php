<?php

use App\Models\Institution;
use App\Models\StaffRequest;
use Inertia\Testing\AssertableInertia as Assert;

test('home page renders with vacancy and institution stats', function () {
    $institution = Institution::factory()->create(['verification_status' => 'verified']);
    StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'published']);
    StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'draft']);

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('stats.vacancies', 1)
            ->where('stats.institutions', 1)
        );
});
