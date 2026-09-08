<?php

namespace App\Jobs\Ai;

use App\Models\Candidate;
use App\Services\Ai\ClaudeClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Killer-фича под рынок КР (ARCHITECTURE.md §4): генерирует резюме
 * кандидата одновременно на русском и кыргызском языках из данных профиля.
 */
class GenerateBilingualResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $candidateId) {}

    public function backoff(): array
    {
        return [10, 30, 90];
    }

    public function handle(ClaudeClient $claude): void
    {
        $candidate = Candidate::with('user')->findOrFail($this->candidateId);

        $schema = [
            'type' => 'object',
            'properties' => [
                'resume_ru' => ['type' => 'string'],
                'resume_ky' => ['type' => 'string'],
            ],
            'required' => ['resume_ru', 'resume_ky'],
            'additionalProperties' => false,
        ];

        $system = 'Ты — ИИ-ассистент по составлению резюме образовательной платформы EduJob. '.
            'Составляешь профессиональное резюме педагога сразу на двух языках — русском и '.
            'кыргызском (resume_ru и resume_ky) — на основе данных профиля. '.
            'Не выдумывай факты, которых нет в профиле. Формат — связный текст резюме, не список полей.';

        $user = sprintf(
            "Имя: %s\nСпециализация/предмет: %s\nКвалификационная категория: %s\n".
            "Уровни образования: %s\nФормат занятости: %s\nГотовность к переезду: %s\nО себе: %s",
            $candidate->user->name,
            $candidate->subject ?? '—',
            $candidate->teaching_category ?? '—',
            implode(', ', $candidate->education_levels ?? []),
            $candidate->employment_type,
            $candidate->relocation_ready ? 'да' : 'нет',
            $candidate->bio ?? '—',
        );

        $result = $claude->structuredCompletion(
            jobClass: self::class,
            system: $system,
            userContent: $user,
            outputSchema: $schema,
            maxTokens: 2048,
            relatedModel: $candidate,
        );

        $candidate->update([
            'resume_text_ru' => $result['resume_ru'],
            'resume_text_ky' => $result['resume_ky'],
        ]);
    }
}
