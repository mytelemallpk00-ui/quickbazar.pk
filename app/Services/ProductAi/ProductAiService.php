<?php

namespace App\Services\ProductAi;

use App\Support\AiSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductAiService
{
    public const PROVIDER_SMART = 'smart';

    public const PROVIDER_OPENAI = 'openai';

    public const PROVIDER_GEMINI = 'gemini';

    public const PROVIDER_GEMINI_FLASH = 'gemini_flash';

    public const PROVIDER_GEMINI_PRO = 'gemini_pro';

    public const PROVIDER_AUTO = 'auto';

    public function isConfigured(?string $provider = self::PROVIDER_AUTO): bool
    {
        return match ($this->normalizeProvider($provider)) {
            self::PROVIDER_SMART => true,
            self::PROVIDER_OPENAI => AiSettings::hasOpenAiKey(),
            self::PROVIDER_GEMINI, self::PROVIDER_GEMINI_FLASH, self::PROVIDER_GEMINI_PRO => AiSettings::hasGeminiKey(),
            default => AiSettings::hasOpenAiKey() || AiSettings::hasGeminiKey() || true,
        };
    }

    public function generate(
        string $productName,
        ?string $targetKeyword = null,
        ?string $specs = null,
        ?string $categoryName = null,
        ?string $provider = self::PROVIDER_GEMINI_FLASH,
    ): ProductAiContent {
        $productName = trim($productName);
        if ($productName === '') {
            throw new ProductAiException('Product name is required for AI generation.');
        }

        $provider = $this->normalizeProvider($provider);
        $targetKeyword = trim((string) $targetKeyword);
        $specs = trim((string) $specs);
        $categoryName = trim((string) $categoryName);

        if ($provider === self::PROVIDER_SMART) {
            return app(ProductSeoFallbackGenerator::class)->generate(
                $productName,
                $targetKeyword !== '' ? $targetKeyword : null,
                $specs !== '' ? $specs : null,
                $categoryName !== '' ? $categoryName : null,
            );
        }

        if (($provider === self::PROVIDER_GEMINI_FLASH || $provider === self::PROVIDER_GEMINI_PRO || $provider === self::PROVIDER_GEMINI)
            && ! AiSettings::hasGeminiKey()) {
            throw new ProductAiException(
                'Gemini API key missing. Open Settings → AI integration and paste your free key from Google AI Studio (https://aistudio.google.com/app/apikey). Gmail sign-in cannot activate API access automatically.'
            );
        }

        if ($provider === self::PROVIDER_OPENAI && ! AiSettings::hasOpenAiKey()) {
            throw new ProductAiException('OpenAI API key missing. Add it under Settings → AI integration.');
        }

        $userPrompt = $this->userPrompt($productName, $targetKeyword, $specs, $categoryName);
        $systemPrompt = $this->systemPrompt();

        $json = match ($provider) {
            self::PROVIDER_GEMINI_FLASH, self::PROVIDER_GEMINI => $this->callGemini($systemPrompt, $userPrompt, (string) config('services.gemini.flash_model', 'gemini-2.0-flash')),
            self::PROVIDER_GEMINI_PRO => $this->callGemini($systemPrompt, $userPrompt, (string) config('services.gemini.model', 'gemini-1.5-pro')),
            self::PROVIDER_OPENAI => $this->callOpenAi($systemPrompt, $userPrompt),
            default => $this->callWithAuto($systemPrompt, $userPrompt),
        };

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            throw new ProductAiException('AI response was not valid JSON.');
        }

        return ProductAiContent::fromArray($decoded);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function enrichProductPayload(
        array $payload,
        string $productName,
        ?string $targetKeyword = null,
        ?string $specs = null,
        ?string $categoryName = null,
        ?string $provider = self::PROVIDER_GEMINI_FLASH,
    ): array {
        try {
            $content = $this->generate($productName, $targetKeyword, $specs, $categoryName, $provider);

            return array_merge($payload, $content->toProductPayload());
        } catch (\Throwable $e) {
            report($e);

            try {
                $fallback = app(ProductSeoFallbackGenerator::class)->generate(
                    $productName,
                    $targetKeyword,
                    $specs,
                    $categoryName,
                );

                return array_merge($payload, $fallback->toProductPayload());
            } catch (\Throwable) {
                return $payload;
            }
        }
    }

    public static function sanitizeHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        return strip_tags($html, '<h2><h3><p><ul><ol><li><strong><em><br>');
    }

    protected function normalizeProvider(?string $provider): string
    {
        $provider = strtolower(trim((string) $provider));

        $allowed = [
            self::PROVIDER_SMART,
            self::PROVIDER_OPENAI,
            self::PROVIDER_GEMINI,
            self::PROVIDER_GEMINI_FLASH,
            self::PROVIDER_GEMINI_PRO,
            self::PROVIDER_AUTO,
        ];

        return in_array($provider, $allowed, true) ? $provider : self::PROVIDER_GEMINI_FLASH;
    }

    protected function callWithAuto(string $systemPrompt, string $userPrompt): string
    {
        if (AiSettings::hasOpenAiKey()) {
            return $this->callOpenAi($systemPrompt, $userPrompt);
        }
        if (AiSettings::hasGeminiKey()) {
            return $this->callGemini($systemPrompt, $userPrompt, (string) config('services.gemini.flash_model', 'gemini-2.0-flash'));
        }

        throw new ProductAiException('No AI API key configured.');
    }

    protected function callOpenAi(string $systemPrompt, string $userPrompt): string
    {
        $response = Http::withToken(AiSettings::openAiKey())
            ->acceptJson()
            ->timeout((int) config('services.openai.timeout', 90))
            ->post((string) config('services.openai.url'), [
                'model' => (string) config('services.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.45,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

        if (! $response->successful()) {
            $message = $response->json('error.message') ?? $response->body();
            throw new ProductAiException('OpenAI failed: '.Str::limit((string) $message, 240));
        }

        $content = $response->json('choices.0.message.content');
        if (! is_string($content) || trim($content) === '') {
            throw new ProductAiException('OpenAI returned an empty response.');
        }

        return trim($content);
    }

    protected function callGemini(string $systemPrompt, string $userPrompt, string $model): string
    {
        $baseUrl = rtrim((string) config('services.gemini.url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $key = AiSettings::geminiKey();

        $response = Http::acceptJson()
            ->timeout((int) config('services.gemini.timeout', 90))
            ->post("{$baseUrl}/models/{$model}:generateContent?key={$key}", [
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $userPrompt]],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.45,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (! $response->successful()) {
            $message = $response->json('error.message') ?? $response->body();
            throw new ProductAiException('Gemini failed: '.Str::limit((string) $message, 240));
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        if (! is_string($text) || trim($text) === '') {
            throw new ProductAiException('Gemini returned an empty response.');
        }

        return trim($text);
    }

    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert SEO and e-commerce copywriter for Beautage, a premium beauty store in Pakistan.
Write natural English suitable for Pakistani customers (PKR pricing context, nationwide delivery, COD-friendly tone).
Return ONLY a single JSON object. No markdown fences, no commentary.
Use this exact schema and keys:
{
  "seo_title": "string, max 70 chars, include focus keyword when provided",
  "meta_description": "string, max 160 chars, compelling SERP snippet",
  "slug": "lowercase hyphenated URL slug, max 80 chars, no special characters",
  "short_description": "string, 2-4 sentences for under product title on PDP",
  "listing_summary": "string, max 300 chars for collection cards",
  "html_description": "string, structured HTML using only h2, h3, p, ul, li, strong, em, br — include benefits and usage",
  "detail_heading": "string, H2-style heading for description tab",
  "bullet_features": [{"title": "string", "description": "string"}],
  "specs_rows": [{"label": "string", "value": "string"}],
  "search_tags": ["string", "..."],
  "seo_focus_keyword": "primary keyword phrase",
  "og_title": "string optional, defaults to seo_title",
  "og_description": "string optional",
  "routine_body": "string optional plain-text how-to-use steps"
}
Rules:
- Integrate the target keyword naturally (avoid stuffing).
- bullet_features: 3-6 items.
- specs_rows: 4-8 rows when specs input exists; otherwise infer sensible beauty-product specs.
- search_tags: 8-15 backend search tags (lowercase phrases).
PROMPT;
    }

    protected function userPrompt(
        string $productName,
        string $targetKeyword,
        string $specs,
        string $categoryName,
    ): string {
        $lines = [
            'Product name: '.$productName,
        ];

        if ($categoryName !== '') {
            $lines[] = 'Category: '.$categoryName;
        }

        if ($targetKeyword !== '') {
            $lines[] = 'Target SEO keyword: '.$targetKeyword;
        }

        if ($specs !== '') {
            $lines[] = 'Product specs / raw details:';
            $lines[] = $specs;
        } else {
            $lines[] = 'Product specs / raw details: (not provided — infer from product name and category).';
        }

        return implode("\n", $lines);
    }
}
