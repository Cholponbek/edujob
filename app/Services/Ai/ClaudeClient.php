<?php

namespace App\Services\Ai;

use Anthropic\Client;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\RateLimitException;
use App\Models\AiUsageLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Тонкая обёртка над официальным Anthropic PHP SDK. Все ИИ-джобы вызывают
 * только этот класс — единая точка для модели/цены (config/services.php)
 * и логирования стоимости (ai_usage_logs), ARCHITECTURE.md §2/§3.
 */
class ClaudeClient
{
    private ?Client $client = null;

    /**
     * @param  string|array<int, array<string, mixed>>  $userContent  Строка — обычный текстовый промпт;
     *                                                                массив — content-блоки (например document/image + text) для файлового ввода.
     * @param  array<string, mixed>  $outputSchema  JSON Schema ответа (Anthropic structured outputs).
     * @return array<string, mixed> Декодированный JSON-ответ модели.
     */
    public function structuredCompletion(
        string $jobClass,
        string $system,
        string|array $userContent,
        array $outputSchema,
        int $maxTokens = 4096,
        ?Model $relatedModel = null,
    ): array {
        try {
            $message = $this->client()->messages->create(
                model: config('services.anthropic.model'),
                maxTokens: $maxTokens,
                system: $system,
                messages: [['role' => 'user', 'content' => $userContent]],
                outputConfig: [
                    'format' => [
                        'type' => 'json_schema',
                        'schema' => $outputSchema,
                    ],
                ],
            );
        } catch (RateLimitException|APIConnectionException|APIStatusException $e) {
            Log::warning('Claude API call failed', [
                'job' => $jobClass,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        $text = null;

        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $text = $block->text;
                break;
            }
        }

        if ($text === null) {
            throw new RuntimeException("Claude API вернул ответ без текстового блока (job: {$jobClass})");
        }

        $data = json_decode($text, true, flags: JSON_THROW_ON_ERROR);

        $this->logUsage($jobClass, $message->usage->inputTokens, $message->usage->outputTokens, $relatedModel);

        return $data;
    }

    private function client(): Client
    {
        return $this->client ??= new Client(apiKey: config('services.anthropic.api_key'));
    }

    private function logUsage(string $jobClass, int $inputTokens, int $outputTokens, ?Model $related): void
    {
        $cost = ($inputTokens / 1_000_000) * config('services.anthropic.input_price_per_million')
            + ($outputTokens / 1_000_000) * config('services.anthropic.output_price_per_million');

        AiUsageLog::create([
            'job_class' => $jobClass,
            'model' => config('services.anthropic.model'),
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'estimated_cost_usd' => round($cost, 4),
            'related_type' => $related?->getMorphClass(),
            'related_id' => $related?->getKey(),
        ]);
    }
}
