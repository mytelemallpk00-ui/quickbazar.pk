<?php

namespace App\Services\Social;

use App\Support\AiSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialContentGenerator
{
    /**
     * @return list<array{title: ?string, caption: string}>
     */
    public function generate(string $category, int $count): array
    {
        $count = max(1, min(50, $count));
        $apiKey = AiSettings::openAiKey();
        if ($apiKey === '') {
            return $this->fallbackBatch($category, $count);
        }

        $prompt = <<<PROMPT
You are a social media strategist for Quick Bazar, a premium beauty & wellness store in Pakistan.
Generate exactly {$count} unique social posts for the category: "{$category}".

Return ONLY valid JSON (no markdown): an array of objects with keys:
- "title" (short hook, max 80 chars, optional for Instagram)
- "caption" (engaging post body, 2-4 sentences, include 5-8 relevant hashtags at the end)

Tone: elegant, trustworthy, conversion-friendly. Mention Pakistan delivery/COD only when natural.
PROMPT;

        $response = Http::withToken($apiKey)
            ->timeout((int) config('services.openai.timeout', 90))
            ->post((string) config('services.openai.url'), [
                'model' => (string) config('social.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.85,
                'messages' => [
                    ['role' => 'system', 'content' => 'You output strict JSON arrays only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('OpenAI: '.Str::limit((string) ($response->json('error.message') ?? $response->body()), 240));
        }

        $content = (string) data_get($response->json(), 'choices.0.message.content', '');
        $content = trim(preg_replace('/^```json\s*|\s*```$/', '', $content) ?? $content);
        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            throw new \RuntimeException('OpenAI returned invalid JSON for social captions.');
        }

        $items = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $caption = trim((string) ($row['caption'] ?? ''));
            if ($caption === '') {
                continue;
            }
            $items[] = [
                'title' => filled($row['title'] ?? null) ? Str::limit((string) $row['title'], 120, '') : null,
                'caption' => $caption,
            ];
        }

        if ($items === []) {
            return $this->fallbackBatch($category, $count);
        }

        return array_slice($items, 0, $count);
    }

    /** @return list<array{title: ?string, caption: string}> */
    protected function fallbackBatch(string $category, int $count): array
    {
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $items[] = [
                'title' => "{$category} pick #{$i}",
                'caption' => "Discover {$category} essentials at Quick Bazar — curated quality, fast delivery across Pakistan. #QuickBazar #{$this->tagify($category)} #Pakistan #Beauty #ShopOnline",
            ];
        }

        return $items;
    }

    protected function tagify(string $category): string
    {
        return Str::studly(preg_replace('/[^a-zA-Z0-9]+/', '', $category) ?? 'Beauty');
    }
}
