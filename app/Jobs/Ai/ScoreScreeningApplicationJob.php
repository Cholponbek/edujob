<?php

namespace App\Jobs\Ai;

use App\Models\Application;
use App\Services\Ai\ClaudeClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

/**
 * Оценивает ответы кандидата на вопросы скрининга (см.
 * GenerateScreeningQuestionsJob) и выставляет вердикт/процент соответствия.
 */
class ScoreScreeningApplicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $applicationId) {}

    public function backoff(): array
    {
        return [10, 30, 90];
    }

    public function handle(ClaudeClient $claude): void
    {
        $application = Application::with(['staffRequest', 'candidate'])->findOrFail($this->applicationId);

        if (empty($application->screening_questions) || empty($application->screening_answers)) {
            // Джоб вызван до генерации вопросов или до ответа кандидата —
            // ошибка вызывающего кода, не транзиентный сбой: ретраи не помогут.
            $this->fail(new RuntimeException("Application {$this->applicationId}: нет вопросов или ответов для оценки"));

            return;
        }

        $staffRequest = $application->staffRequest;
        $candidate = $application->candidate;

        $qa = collect($application->screening_questions)
            ->map(fn (string $question, int $i) => sprintf(
                "Вопрос: %s\nОтвет: %s",
                $question,
                $application->screening_answers[$i] ?? '(нет ответа)'
            ))
            ->implode("\n\n");

        $schema = [
            'type' => 'object',
            'properties' => [
                'verdict' => ['type' => 'string', 'enum' => ['green', 'yellow', 'red']],
                'match_percent' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100],
                'reasoning' => ['type' => 'string'],
            ],
            'required' => ['verdict', 'match_percent', 'reasoning'],
            'additionalProperties' => false,
        ];

        $system = 'Ты — HR-ассистент образовательной платформы EduJob (Кыргызстан). '.
            'Оцениваешь ответы кандидата на вопросы скрининга и выставляешь вердикт: '.
            'green (хорошее соответствие), yellow (частичное), red (не подходит), '.
            'а также процент соответствия вакансии и краткое обоснование на русском языке.';

        $user = sprintf(
            "Вакансия: %s\nПредмет: %s\nУровень образования: %s\nТребуемая категория: %s\n\n".
            "Вопросы и ответы кандидата:\n%s",
            $staffRequest->title,
            $staffRequest->subject ?? '—',
            $staffRequest->education_level,
            $staffRequest->required_category ?? '—',
            $qa,
        );

        $result = $claude->structuredCompletion(
            jobClass: self::class,
            system: $system,
            userContent: $user,
            outputSchema: $schema,
            maxTokens: 1024,
            relatedModel: $application,
        );

        $application->update([
            'screening_verdict' => $result['verdict'],
            'screening_match_percent' => $result['match_percent'],
            'screened_at' => now(),
            'status' => 'screened',
        ]);
    }
}
