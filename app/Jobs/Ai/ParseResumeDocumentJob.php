<?php

namespace App\Jobs\Ai;

use App\Models\Candidate;
use App\Services\Ai\ClaudeClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Разбирает загруженное резюме (PDF или изображение) через Claude vision и
 * заполняет структурированные поля профиля кандидата.
 *
 * Известное ограничение: DOC/DOCX не поддерживаются — Claude принимает
 * файлы как PDF-документ или изображение, текстовый Word-формат нужно
 * сперва сконвертировать в PDF на стороне клиента/сервера (не реализовано
 * в этом проходе, см. ARCHITECTURE.md).
 */
class ParseResumeDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const SUPPORTED_MIME_TYPES = [
        'application/pdf' => 'document',
        'image/jpeg' => 'image',
        'image/png' => 'image',
    ];

    public int $tries = 3;

    public function __construct(public int $candidateId) {}

    public function backoff(): array
    {
        return [10, 30, 90];
    }

    public function handle(ClaudeClient $claude): void
    {
        $candidate = Candidate::findOrFail($this->candidateId);

        if (empty($candidate->resume_source_path)) {
            $this->fail(new RuntimeException("Candidate {$this->candidateId}: resume_source_path не задан"));

            return;
        }

        $disk = Storage::disk('public');
        $mimeType = $disk->mimeType($candidate->resume_source_path);
        $blockType = self::SUPPORTED_MIME_TYPES[$mimeType] ?? null;

        if ($blockType === null) {
            $this->fail(new RuntimeException(
                "Candidate {$this->candidateId}: формат {$mimeType} не поддерживается (нужен PDF/JPG/PNG)"
            ));

            return;
        }

        $base64 = base64_encode($disk->get($candidate->resume_source_path));

        $schema = [
            'type' => 'object',
            'properties' => [
                'subject' => ['type' => ['string', 'null']],
                'education_levels' => [
                    'type' => 'array',
                    'items' => ['type' => 'string', 'enum' => ['preschool', 'primary', 'secondary', 'vocational', 'higher']],
                ],
                'teaching_category' => ['type' => ['string', 'null']],
                'bio' => ['type' => 'string'],
            ],
            'required' => ['subject', 'education_levels', 'teaching_category', 'bio'],
            'additionalProperties' => false,
        ];

        $system = 'Ты — ИИ-ассистент образовательной платформы EduJob (Кыргызстан). '.
            'Извлекаешь из загруженного резюме педагога структурированные данные: '.
            'предмет/специализацию, уровни образования, с которыми может работать, '.
            'квалификационную категорию (если указана) и краткое био на русском языке. '.
            'Не выдумывай данные, которых нет в документе — оставляй null/пусто, если неизвестно.';

        $result = $claude->structuredCompletion(
            jobClass: self::class,
            system: $system,
            userContent: [
                [
                    'type' => $blockType,
                    'source' => [
                        'type' => 'base64',
                        'media_type' => $mimeType,
                        'data' => $base64,
                    ],
                ],
                ['type' => 'text', 'text' => 'Извлеки данные из этого резюме.'],
            ],
            outputSchema: $schema,
            maxTokens: 2048,
            relatedModel: $candidate,
        );

        $candidate->update([
            'subject' => $result['subject'],
            'education_levels' => $result['education_levels'],
            'teaching_category' => $result['teaching_category'],
            'bio' => $result['bio'],
        ]);
    }
}
