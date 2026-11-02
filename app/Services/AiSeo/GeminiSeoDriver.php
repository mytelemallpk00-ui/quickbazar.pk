<?php

namespace App\Services\AiSeo;

use App\Services\AiSeo\Concerns\BuildsAiSeoPrompt;
use App\Services\ProductAi\ProductAiException;
use App\Support\AiSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeminiSeoDriver implements AiSeoDriver
{
    use BuildsAiSeoPrompt;

    public function __construct(protected bool $useProModel = true) {}

    public function generate(string $productName, string $description, string $specs, ?string $targetKeyword = null): AiSeoPayload
    {
        if (! AiSettings::hasGeminiKey()) {
            throw new ProductAiException('Gemini API key is not configured.');
        }

        $model = $this->useProModel
            ? (string) config('services.gemini.model', 'gemini-1.5-pro')
            : (string) config('services.gemini.flash_model', 'gemini-2.0-flash');

        $baseUrl = rtrim((string) config('services.gemini.url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $key = AiSettings::geminiKey();

        $response = Http::acceptJson()
            ->timeout((int) config('services.gemini.timeout', 90))
            ->post("{$baseUrl}/models/{$model}:generateContent?key={$key}", [
                'systemInstruction' => ['parts' => [['text' => $this->systemPrompt()]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $this->userPrompt($productName, $description, $specs, $targetKeyword)]]]],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (! $response->successful()) {
            throw new ProductAiException('Gemini: '.Str::limit((string) ($response->json('error.message') ?? $response->body()), 200));
        }

        $json = $response->json('candidates.0.content.parts.0.text');
        if (! is_string($json)) {
            throw new ProductAiException('Gemini returned empty content.');
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            throw new ProductAiException('Gemini returned invalid JSON.');
        }

        return AiSeoPayload::fromArray($decoded);
    }
}
