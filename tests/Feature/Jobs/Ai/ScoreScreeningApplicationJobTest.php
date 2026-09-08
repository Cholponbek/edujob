<?php

use App\Jobs\Ai\ScoreScreeningApplicationJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\StaffRequest;
use App\Services\Ai\ClaudeClient;

test('it scores answers and stores the verdict', function () {
    $application = Application::factory()
        ->for(StaffRequest::factory())
        ->for(Candidate::factory())
        ->create([
            'status' => 'screening',
            'screening_questions' => ['Q1?', 'Q2?'],
            'screening_answers' => ['A1', 'A2'],
        ]);

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldReceive('structuredCompletion')
            ->once()
            ->andReturn(['verdict' => 'green', 'match_percent' => 87, 'reasoning' => 'Хорошо ответил']);
    });

    (new ScoreScreeningApplicationJob($application->id))->handle(app(ClaudeClient::class));

    $application->refresh();

    expect($application->screening_verdict)->toBe('green');
    expect($application->screening_match_percent)->toBe(87);
    expect($application->status)->toBe('screened');
    expect($application->screened_at)->not->toBeNull();
});

test('it fails without retry when there are no questions or answers', function () {
    $application = Application::factory()
        ->for(StaffRequest::factory())
        ->for(Candidate::factory())
        ->create(['status' => 'applied']);

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldNotReceive('structuredCompletion');
    });

    $job = new ScoreScreeningApplicationJob($application->id);
    $job->handle(app(ClaudeClient::class));

    $application->refresh();
    expect($application->status)->toBe('applied');
});
