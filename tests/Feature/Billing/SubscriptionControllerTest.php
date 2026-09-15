<?php

use App\Models\Institution;
use App\Models\Plan;
use App\Models\User;

test('institution staff can subscribe to a plan', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    $institution->users()->attach($user, ['role' => 'director']);
    $plan = Plan::factory()->create();

    $response = $this->actingAs($user)->post('/institution/subscriptions', ['plan_id' => $plan->id]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertDatabaseHas('subscriptions', [
        'institution_id' => $institution->id,
        'plan_id' => $plan->id,
        'status' => 'pending',
    ]);
});

test('a user with no institution cannot subscribe', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();

    $response = $this->actingAs($user)->post('/institution/subscriptions', ['plan_id' => $plan->id]);

    $response->assertForbidden();
});

test('an inactive plan cannot be subscribed to', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();
    $institution->users()->attach($user, ['role' => 'director']);
    $plan = Plan::factory()->create(['is_active' => false]);

    $response = $this->actingAs($user)->post('/institution/subscriptions', ['plan_id' => $plan->id]);

    $response->assertNotFound();
});
