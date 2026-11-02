<?php

namespace App\Services\AiSeo;

use App\Services\AiSeo\Concerns\BuildsAiSeoPrompt;
use App\Services\ProductAi\ProductAiException;
use App\Support\AiSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OpenAiSeoDriver implements AiSeoDriver
{
    use BuildsAiSeoPrompt;

    public function generate(string $productName, string $description, string $specs, ?string $targetKeyword = null): AiSeoPayload
    {
        if (! AiSettings::hasOpenAiKey()) {
            throw new ProductAiException('OpenAI API key is not configured.');
        }

        $response = Http::withToken(AiSettings::openAiKey())
            ->acceptJson()
            ->timeout((int) config('services.openai.timeout', 90))
            ->post((string) config('services.openai.url'), [
                'model' => (string) config('services.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user', 'content' => $this->userPrompt($productName, $description, $specs, $targetKeyword)],
                ],
            ]);

        if (! $response->successful()) {
            throw new ProductAiException('OpenAI: '.Str::limit((string) ($response->json('error.message') ?? $response->body()), 200));
        }

        $json = $response->json('choices.0.message.content');
        if (! is_string($json)) {
            throw new ProductAiException('OpenAI returned empty content.');
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            throw new ProductAiException('OpenAI returned invalid JSON.');
        }

        return AiSeoPayload::fromArray($decoded);
    }
}
