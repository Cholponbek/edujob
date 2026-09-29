<?php

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Institution;
use App\Models\StaffRequest;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('index lists only published vacancies', function () {
    $institution = Institution::factory()->create();
    $published = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'published']);
    StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'draft']);

    $this->get('/vacancies')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Vacancies/Index')
            ->has('staffRequests.data', 1)
            ->where('staffRequests.data.0.id', $published->id)
        );
});

test('index filters by subject', function () {
    $institution = Institution::factory()->create();
    StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'published', 'subject' => 'Математика']);
    StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'published', 'subject' => 'Физика']);

    $this->get('/vacancies?subject=Математика')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Vacancies/Index')
            ->has('staffRequests.data', 1)
            ->where('staffRequests.data.0.subject', 'Математика')
        );
});

test('show returns the vacancy detail for a published request', function () {
    $institution = Institution::factory()->create();
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'published']);

    $this->get("/vacancies/{$staffRequest->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Vacancies/Show')
            ->where('staffRequest.id', $staffRequest->id)
            ->where('hasApplied', false)
        );
});

test('show 404s for a draft vacancy', function () {
    $institution = Institution::factory()->create();
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'draft']);

    $this->get("/vacancies/{$staffRequest->id}")->assertNotFound();
});

test('show reports hasApplied for a candidate who already applied', function () {
    $institution = Institution::factory()->create();
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'published']);
    $user = User::factory()->create();
    $candidate = Candidate::factory()->create(['user_id' => $user->id]);
    Application::factory()->create(['staff_request_id' => $staffRequest->id, 'candidate_id' => $candidate->id]);

    $this->actingAs($user)
        ->get("/vacancies/{$staffRequest->id}")
        ->assertInertia(fn (Assert $page) => $page->where('hasApplied', true));
});
