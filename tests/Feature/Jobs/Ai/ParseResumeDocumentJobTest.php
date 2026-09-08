<?php

use App\Jobs\Ai\ParseResumeDocumentJob;
use App\Models\Candidate;
use App\Services\Ai\ClaudeClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it parses a pdf resume and updates the candidate profile', function () {
    Storage::fake('public');

    $candidate = Candidate::factory()->create();
    $path = Storage::disk('public')->putFile('resumes', UploadedFile::fake()->create('resume.pdf', 10, 'application/pdf'));
    $candidate->update(['resume_source_path' => $path]);

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldReceive('structuredCompletion')
            ->once()
            ->andReturn([
                'subject' => 'Математика',
                'education_levels' => ['secondary'],
                'teaching_category' => 'первая',
                'bio' => 'Опытный педагог',
            ]);
    });

    (new ParseResumeDocumentJob($candidate->id))->handle(app(ClaudeClient::class));

    $candidate->refresh();

    expect($candidate->subject)->toBe('Математика');
    expect($candidate->education_levels)->toBe(['secondary']);
    expect($candidate->bio)->toBe('Опытный педагог');
});

test('it fails without retry for unsupported file types', function () {
    Storage::fake('public');

    $candidate = Candidate::factory()->create(['subject' => 'Физика']);
    $path = Storage::disk('public')->putFile('resumes', UploadedFile::fake()->create('resume.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'));
    $candidate->update(['resume_source_path' => $path]);

    $this->mock(ClaudeClient::class, function ($mock) {
        $mock->shouldNotReceive('structuredCompletion');
    });

    (new ParseResumeDocumentJob($candidate->id))->handle(app(ClaudeClient::class));

    $candidate->refresh();
    expect($candidate->subject)->toBe('Физика');
});
