<?php

use App\Models\Application;
use App\Models\Institution;
use App\Models\StaffRequest;
use App\Models\User;

test('institution staff can view applications for their own vacancy', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    $institution->users()->attach($user, ['role' => 'hr']);
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id]);
    Application::factory()->create(['staff_request_id' => $staffRequest->id]);

    $response = $this->actingAs($user)->getJson("/institution/staff-requests/{$staffRequest->id}/applications");

    $response->assertOk();
    expect($response->json())->toHaveCount(1);
});

test('institution staff can mark an applicant as hired', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    $institution->users()->attach($user, ['role' => 'hr']);
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id]);
    $application = Application::factory()->create(['staff_request_id' => $staffRequest->id, 'status' => 'screened']);

    $response = $this->actingAs($user)->patch("/institution/applications/{$application->id}", ['status' => 'hired']);

    $response->assertSessionHasNoErrors();
    expect($application->fresh()->status)->toBe('hired');
});

test('a user from another institution cannot view or update applications', function () {
    $institution = Institution::factory()->create();
    $otherInstitution = Institution::factory()->create();
    $user = User::factory()->create();
    $institution->users()->attach($user, ['role' => 'hr']);

    $foreignStaffRequest = StaffRequest::factory()->create(['institution_id' => $otherInstitution->id]);
    $foreignApplication = Application::factory()->create(['staff_request_id' => $foreignStaffRequest->id]);

    $this->actingAs($user)
        ->getJson("/institution/staff-requests/{$foreignStaffRequest->id}/applications")
        ->assertForbidden();

    $this->actingAs($user)
        ->patch("/institution/applications/{$foreignApplication->id}", ['status' => 'hired'])
        ->assertForbidden();
});
