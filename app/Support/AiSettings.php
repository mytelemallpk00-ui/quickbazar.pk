<?php

namespace App\Support;

class AiSettings
{
    public const GROUP = 'ai';

    public static function openAiKey(): string
    {
        $stored = trim((string) SiteSettings::get('ai_openai_api_key', ''));

        return $stored !== '' ? $stored : trim((string) config('services.openai.key', ''));
    }

    public static function geminiKey(): string
    {
        $stored = trim((string) SiteSettings::get('ai_gemini_api_key', ''));

        return $stored !== '' ? $stored : trim((string) config('services.gemini.key', ''));
    }

    public static function hasOpenAiKey(): bool
    {
        return static::openAiKey() !== '';
    }

    public static function hasGeminiKey(): bool
    {
        return static::geminiKey() !== '';
    }

    public static function seoProvider(): string
    {
        $stored = strtolower(trim((string) SiteSettings::get('ai_seo_provider', '')));
        if (in_array($stored, ['openai', 'gemini', 'smart'], true)) {
            return $stored;
        }

        $env = strtolower(trim((string) config('services.ai_seo.default_provider', 'smart')));

        return in_array($env, ['openai', 'gemini', 'smart'], true) ? $env : 'smart';
    }

    public static function setSeoProvider(string $provider): void
    {
        $provider = strtolower(trim($provider));
        if (! in_array($provider, ['openai', 'gemini', 'smart'], true)) {
            $provider = 'smart';
        }

        SiteSettings::set('ai_seo_provider', $provider, self::GROUP);
    }

    /** @return array<string, string> */
    public static function forAdminForm(): array
    {
        return [
            'ai_openai_api_key' => static::maskKey(static::openAiKey()),
            'ai_gemini_api_key' => static::maskKey(static::geminiKey()),
            'ai_openai_api_key_input' => '',
            'ai_gemini_api_key_input' => '',
        ];
    }

    public static function saveFromAdmin(array $state): void
    {
        $openAi = trim((string) ($state['ai_openai_api_key_input'] ?? ''));
        if ($openAi !== '') {
            SiteSettings::set('ai_openai_api_key', $openAi, self::GROUP);
        }

        $gemini = trim((string) ($state['ai_gemini_api_key_input'] ?? ''));
        if ($gemini !== '') {
            SiteSettings::set('ai_gemini_api_key', $gemini, self::GROUP);
        }
    }

    public static function maskKey(string $key): string
    {
        if ($key === '') {
            return '';
        }

        if (strlen($key) <= 8) {
            return str_repeat('•', strlen($key));
        }

        return substr($key, 0, 4).str_repeat('•', max(4, strlen($key) - 8)).substr($key, -4);
    }
}
