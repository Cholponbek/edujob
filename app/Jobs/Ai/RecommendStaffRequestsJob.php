<?php

namespace App\Jobs\Ai;

use App\Models\Candidate;
use App\Models\CandidateRecommendation;
use App\Models\StaffRequest;
use App\Services\Ai\ClaudeClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Ранжирует опубликованные вакансии под профиль кандидата
 * (ARCHITECTURE.md §3 — ИИ-подбор вакансий).
 */
class RecommendStaffRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const MAX_CANDIDATES_CONSIDERED = 30;

    private const MAX_RECOMMENDATIONS = 10;

    public int $tries = 3;

    public function __construct(public int $candidateId) {}

    public function backoff(): array
    {
        return [10, 30, 90];
    }

    public function handle(ClaudeClient $claude): void
    {
        $candidate = Candidate::findOrFail($this->candidateId);

        $appliedStaffRequestIds = $candidate->applications()->pluck('staff_request_id');

        $staffRequests = StaffRequest::query()
            ->where('status', 'published')
            ->whereNotIn('id', $appliedStaffRequestIds)
            ->latest('published_at')
            ->limit(self::MAX_CANDIDATES_CONSIDERED)
            ->get(['id', 'title', 'subject', 'education_level', 'employment_type', 'stake_fraction']);

        if ($staffRequests->isEmpty()) {
            return;
        }

        $schema = [
            'type' => 'object',
            'properties' => [
                'recommendations' => [
                    'type' => 'array',
                    'maxItems' => self::MAX_RECOMMENDATIONS,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'staff_request_id' => ['type' => 'integer'],
                            'score' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100],
                            'reasoning' => ['type' => 'string'],
                        ],
                        'required' => ['staff_request_id', 'score', 'reasoning'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'required' => ['recommendations'],
            'additionalProperties' => false,
        ];

        $system = 'Ты — ИИ-ассистент образовательной платформы EduJob (Кыргызстан). '.
            'Ранжируешь список открытых вакансий по соответствию профилю кандидата. '.
            'Возвращай только реально подходящие вакансии (не все подряд), с оценкой '.
            'соответствия 0-100 и коротким обоснованием на русском.';

        $vacanciesList = $staffRequests->map(fn (StaffRequest $sr) => sprintf(
            'id=%d | %s | предмет: %s | уровень: %s | занятость: %s (%.2f ставки)',
            $sr->id,
            $sr->title,
            $sr->subject ?? '—',
            $sr->education_level,
            $sr->employment_type,
            $sr->stake_fraction,
        ))->implode("\n");

        $user = sprintf(
            "Профиль кандидата:\nСпециализация: %s\nКатегория: %s\nУровни образования: %s\n".
            "Формат занятости: %s\nГотовность к переезду: %s\n\nОткрытые вакансии:\n%s",
            $candidate->subject ?? '—',
            $candidate->teaching_category ?? '—',
            implode(', ', $candidate->education_levels ?? []),
            $candidate->employment_type,
            $candidate->relocation_ready ? 'да' : 'нет',
            $vacanciesList,
        );

        $result = $claude->structuredCompletion(
            jobClass: self::class,
            system: $system,
            userContent: $user,
            outputSchema: $schema,
            maxTokens: 2048,
            relatedModel: $candidate,
        );

        $validStaffRequestIds = $staffRequests->pluck('id')->all();

        foreach ($result['recommendations'] as $recommendation) {
            if (! in_array($recommendation['staff_request_id'], $validStaffRequestIds, true)) {
                // Модель сослалась на id, которого не было в списке — пропускаем,
                // не даём выдуманной ссылке попасть в БД.
                continue;
            }

            CandidateRecommendation::updateOrCreate(
                [
                    'candidate_id' => $candidate->id,
                    'staff_request_id' => $recommendation['staff_request_id'],
                ],
                [
                    'score' => $recommendation['score'],
                    'reasoning' => $recommendation['reasoning'],
                    'generated_at' => now(),
                ]
            );
        }
    }
}
