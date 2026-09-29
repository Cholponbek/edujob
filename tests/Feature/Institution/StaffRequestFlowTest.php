<?php

use App\Models\Institution;
use App\Models\StaffRequest;
use App\Models\Subscription;
use App\Models\User;

function attachInstitutionUser(Institution $institution, User $user, string $role): void
{
    $institution->users()->attach($user, ['role' => $role]);
}

function baseStaffRequestPayload(): array
{
    return [
        'title' => 'Учитель математики',
        'subject' => 'Математика',
        'education_level' => 'secondary',
        'employment_type' => 'full_time',
        'stake_fraction' => 1.0,
    ];
}

test('any institution staff member can create a draft vacancy', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    attachInstitutionUser($institution, $user, 'staff');

    $response = $this->actingAs($user)->post('/institution/staff-requests', baseStaffRequestPayload());

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('staff_requests', [
        'institution_id' => $institution->id,
        'title' => 'Учитель математики',
        'status' => 'draft',
    ]);
});

test('a user with no institution cannot create a vacancy', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/institution/staff-requests', baseStaffRequestPayload());

    $response->assertForbidden();
});

test('staff role cannot publish a vacancy', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    attachInstitutionUser($institution, $user, 'staff');
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'draft']);

    $response = $this->actingAs($user)->post("/institution/staff-requests/{$staffRequest->id}/publish");

    $response->assertForbidden();
    expect($staffRequest->fresh()->status)->toBe('draft');
});

test('director cannot publish without an active subscription', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    attachInstitutionUser($institution, $user, 'director');
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'draft']);

    $response = $this->actingAs($user)->post("/institution/staff-requests/{$staffRequest->id}/publish");

    $response->assertStatus(402);
    expect($staffRequest->fresh()->status)->toBe('draft');
});

test('hr can publish a vacancy once the institution has an active subscription', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    attachInstitutionUser($institution, $user, 'hr');
    Subscription::factory()->active()->create(['institution_id' => $institution->id]);
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'draft']);

    $response = $this->actingAs($user)->post("/institution/staff-requests/{$staffRequest->id}/publish");

    $response->assertSessionHasNoErrors();
    $staffRequest->refresh();
    expect($staffRequest->status)->toBe('published');
    expect($staffRequest->published_at)->not->toBeNull();
});

test('a user from another institution cannot update or publish someone else\'s vacancy', function () {
    $ownInstitution = Institution::factory()->create();
    $otherInstitution = Institution::factory()->create();
    $user = User::factory()->create();
    attachInstitutionUser($ownInstitution, $user, 'director');
    Subscription::factory()->active()->create(['institution_id' => $ownInstitution->id]);

    $foreignStaffRequest = StaffRequest::factory()->create(['institution_id' => $otherInstitution->id, 'status' => 'draft']);

    $this->actingAs($user)
        ->patch("/institution/staff-requests/{$foreignStaffRequest->id}", baseStaffRequestPayload())
        ->assertForbidden();

    $this->actingAs($user)
        ->post("/institution/staff-requests/{$foreignStaffRequest->id}/publish")
        ->assertForbidden();
});

test('a closed vacancy can no longer be updated', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    attachInstitutionUser($institution, $user, 'director');
    $staffRequest = StaffRequest::factory()->create(['institution_id' => $institution->id, 'status' => 'closed']);

    $response = $this->actingAs($user)->patch("/institution/staff-requests/{$staffRequest->id}", baseStaffRequestPayload());

    $response->assertForbidden();
});
