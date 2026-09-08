<?php

use App\Jobs\Ai\GenerateScreeningQuestionsJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\StaffRequest;
use App\Services\Ai\ClaudeClient;

test('it generates and stores screening questions', function () {
    $application = Application::factory()
        ->for(StaffRequest::factory())
        ->for(Candidate::factory())
        ->create(['status' => 'applied']);

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldReceive('structuredCompletion')
            ->once()
            ->andReturn(['questions' => ['Q1?', 'Q2?', 'Q3?', 'Q4?']]);
    });

    (new GenerateScreeningQuestionsJob($application->id))->handle(app(ClaudeClient::class));

    $application->refresh();

    expect($application->screening_questions)->toBe(['Q1?', 'Q2?', 'Q3?', 'Q4?']);
    expect($application->status)->toBe('screening');
});
