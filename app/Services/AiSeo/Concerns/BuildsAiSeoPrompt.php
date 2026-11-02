<?php

namespace App\Services\AiSeo\Concerns;

trait BuildsAiSeoPrompt
{
    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert SEO copywriter for Beautage, a beauty e-commerce store in Pakistan.
Return ONLY valid JSON with exactly these keys:
{
  "seo_title": "string max 70 chars",
  "meta_description": "string max 160 chars",
  "slug": "lowercase hyphenated url slug max 80 chars",
  "html_description": "HTML using only h2,h3,p,ul,li,strong,em,br",
  "bullet_points": ["string", "..."],
  "seo_tags": ["lowercase search tag", "..."]
}
Rules: 4-8 bullet_points, 8-15 seo_tags, natural keyword use, Pakistan/COD context.
PROMPT;
    }

    protected function userPrompt(string $name, string $description, string $specs, ?string $keyword): string
    {
        $lines = ['Product name: '.$name, 'Description: '.($description !== '' ? $description : '(none)')];
        if ($keyword) {
            $lines[] = 'Target keyword: '.$keyword;
        }
        $lines[] = 'Specs: '.($specs !== '' ? $specs : '(infer from name)');

        return implode("\n", $lines);
    }
}
