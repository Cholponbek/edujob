<?php

namespace App\Jobs\Ai;

use App\Models\Application;
use App\Services\Ai\ClaudeClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Генерирует 4-5 вопросов для скрининга отклика с учётом предмета и уровня
 * образования вакансии (ARCHITECTURE.md, чеклист "ИИ"). Всегда асинхронно —
 * никогда не вызывается синхронно из HTTP-запроса.
 */
class GenerateScreeningQuestionsJob implements ShouldQueue
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
        $staffRequest = $application->staffRequest;
        $candidate = $application->candidate;

        $schema = [
            'type' => 'object',
            'properties' => [
                'questions' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'minItems' => 4,
                    'maxItems' => 5,
                ],
            ],
            'required' => ['questions'],
            'additionalProperties' => false,
        ];

        $system = 'Ты — HR-ассистент образовательной платформы EduJob (Кыргызстан). '.
            'Составляешь короткие профессиональные вопросы для скрининга кандидата на вакансию '.
            'в сфере образования. Вопросы должны реально проверять компетентность по предмету '.
            'и уровню образования вакансии, без общих формулировок. Пиши на русском языке.';

        $user = sprintf(
            "Вакансия: %s\nПредмет: %s\nУровень образования: %s\nТребуемая категория: %s\n\n".
            "Профиль кандидата:\nСпециализация: %s\nКатегория: %s\n\n".
            'Составь 4-5 вопросов для скрининга этого кандидата под эту вакансию.',
            $staffRequest->title,
            $staffRequest->subject ?? '—',
            $staffRequest->education_level,
            $staffRequest->required_category ?? '—',
            $candidate->subject ?? '—',
            $candidate->teaching_category ?? '—',
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
            'screening_questions' => $result['questions'],
            'status' => 'screening',
        ]);
    }
}
