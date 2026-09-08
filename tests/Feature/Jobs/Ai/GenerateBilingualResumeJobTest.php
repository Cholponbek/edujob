<?php

use App\Jobs\Ai\GenerateBilingualResumeJob;
use App\Models\Candidate;
use App\Services\Ai\ClaudeClient;

test('it generates and stores bilingual resume text', function () {
    $candidate = Candidate::factory()->create();

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldReceive('structuredCompletion')
            ->once()
            ->andReturn(['resume_ru' => 'Резюме на русском', 'resume_ky' => 'Кыргызча резюме']);
    });

    (new GenerateBilingualResumeJob($candidate->id))->handle(app(ClaudeClient::class));

    $candidate->refresh();

    expect($candidate->resume_text_ru)->toBe('Резюме на русском');
    expect($candidate->resume_text_ky)->toBe('Кыргызча резюме');
});
