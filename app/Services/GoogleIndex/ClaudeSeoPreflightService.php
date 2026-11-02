<?php

namespace App\Services\GoogleIndex;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ClaudeSeoPreflightService
{
    public function isConfigured(): bool
    {
        return filled(config('services.anthropic.key'));
    }

    /**
     * @param  array{title?: string, description?: string, url?: string, focus_keyword?: string}  $context
     * @return array{score: int, ready: bool, summary: string, suggestions: list<string>}
     */
    public function analyze(array $context): array
    {
        if (! $this->isConfigured()) {
            throw new GoogleIndexException('Claude API key is not configured. Add ANTHROPIC_API_KEY to your .env file.');
        }

        $response = Http::withHeaders([
            'x-api-key' => (string) config('services.anthropic.key'),
            'anthropic-version' => (string) config('services.anthropic.version', '2023-06-01'),
            'content-type' => 'application/json',
        ])
            ->timeout((int) config('services.anthropic.timeout', 60))
            ->post((string) config('services.anthropic.url'), [
                'model' => (string) config('services.anthropic.model', 'claude-sonnet-4-20250514'),
                'max_tokens' => 1024,
                'temperature' => 0.2,
                'system' => 'You are an SEO quality reviewer for e-commerce product pages. Respond ONLY with valid JSON: {"score":0-100,"ready":boolean,"summary":"one sentence","suggestions":["..."]}. Score readiness for Google indexing (title, meta description, clarity, keyword use).',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                    ],
                ],
            ]);

        if (! $response->successful()) {
            $msg = (string) ($response->json('error.message') ?? $response->body());
            throw GoogleIndexException::fromApi($response->status(), Str::limit($msg, 300));
        }

        $text = $response->json('content.0.text');
        if (! is_string($text)) {
            throw new GoogleIndexException('Claude returned empty analysis.');
        }

        $decoded = json_decode($text, true);
        if (! is_array($decoded)) {
            if (preg_match('/\{.*\}/s', $text, $matches)) {
                $decoded = json_decode($matches[0], true);
            }
        }

        if (! is_array($decoded)) {
            throw new GoogleIndexException('Claude returned invalid JSON for SEO analysis.');
        }

        return [
            'score' => (int) ($decoded['score'] ?? 0),
            'ready' => (bool) ($decoded['ready'] ?? false),
            'summary' => (string) ($decoded['summary'] ?? ''),
            'suggestions' => array_values(array_filter((array) ($decoded['suggestions'] ?? []))),
        ];
    }
}
