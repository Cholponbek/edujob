<?php

use App\Models\Candidate;
use App\Models\Institution;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('candidate dashboard shows profile completion and empty states', function () {
    $user = User::factory()->create();
    Candidate::factory()->create([
        'user_id' => $user->id,
        'subject' => 'Математика',
        'teaching_category' => null,
        'education_levels' => null,
        'bio' => null,
        'diploma_document_path' => null,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Candidate/Dashboard')
            ->where('profileCompletion', 20)
            ->has('applications', 0)
            ->has('recommendations', 0)
        );
});

test('institution staff see a placeholder dashboard instead of the candidate one', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    $institution->users()->attach($user, ['role' => 'director']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('isInstitutionUser', true)
        );
});
