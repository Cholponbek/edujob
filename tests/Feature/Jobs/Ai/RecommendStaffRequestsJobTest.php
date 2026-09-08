<?php

use App\Jobs\Ai\RecommendStaffRequestsJob;
use App\Models\Candidate;
use App\Models\CandidateRecommendation;
use App\Models\StaffRequest;
use App\Services\Ai\ClaudeClient;

test('it stores recommendations returned by the model', function () {
    $candidate = Candidate::factory()->create();
    $staffRequest = StaffRequest::factory()->create(['status' => 'published']);

    $this->mock(ClaudeClient::class, function ($mock) use ($staffRequest) {
        $mock->shouldReceive('structuredCompletion')
            ->once()
            ->andReturn(['recommendations' => [
                ['staff_request_id' => $staffRequest->id, 'score' => 92, 'reasoning' => 'Отличное совпадение'],
            ]]);
    });

    (new RecommendStaffRequestsJob($candidate->id))->handle(app(ClaudeClient::class));

    $this->assertDatabaseHas('candidate_recommendations', [
        'candidate_id' => $candidate->id,
        'staff_request_id' => $staffRequest->id,
        'score' => 92,
    ]);
});

test('it ignores recommendations for staff requests outside the candidate list', function () {
    $candidate = Candidate::factory()->create();
    StaffRequest::factory()->create(['status' => 'published']);

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldReceive('structuredCompletion')
            ->once()
            ->andReturn(['recommendations' => [
                ['staff_request_id' => 999999, 'score' => 50, 'reasoning' => 'Выдумано'],
            ]]);
    });

    (new RecommendStaffRequestsJob($candidate->id))->handle(app(ClaudeClient::class));

    expect(CandidateRecommendation::count())->toBe(0);
});

test('it skips the api call when there are no published staff requests', function () {
    $candidate = Candidate::factory()->create();

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldNotReceive('structuredCompletion');
    });

    (new RecommendStaffRequestsJob($candidate->id))->handle(app(ClaudeClient::class));

    expect(CandidateRecommendation::count())->toBe(0);
});
