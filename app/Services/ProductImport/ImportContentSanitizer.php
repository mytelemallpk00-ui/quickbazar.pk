<?php

namespace App\Services\ProductImport;

use Illuminate\Support\Str;

class ImportContentSanitizer
{
    public function cleanDescription(string $htmlOrText, ?string $sourceUrl = null): string
    {
        $text = html_entity_decode(trim($htmlOrText));
        if ($text === '') {
            return '';
        }

        $text = $this->stripAnchorTags($text);
        $text = strip_tags($text);
        $text = $this->removeBareUrls($text);
        $text = $this->removeSourceReferences($text, $sourceUrl);

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    /** Plain text for AI/specs — no source URL appended by caller. */
    public function cleanForAiSpecs(string $htmlOrText, ?string $sourceUrl = null): string
    {
        return $this->cleanDescription($htmlOrText, $sourceUrl);
    }

    /** Product detail HTML: keep basic tags, drop links and source mentions. */
    public function cleanHtml(string $html, ?string $sourceUrl = null): string
    {
        $html = html_entity_decode(trim($html));
        if ($html === '') {
            return '';
        }

        $html = $this->stripAnchorTags($html);
        $html = $this->removeBareUrls($html);
        $html = strip_tags($html, '<h2><h3><p><ul><ol><li><strong><em><br>');
        $html = $this->removeSourceReferences($html, $sourceUrl);

        return trim($html);
    }

    private function stripAnchorTags(string $html): string
    {
        $result = preg_replace_callback('/<a\b[^>]*>(.*?)<\/a>/is', function (array $m): string {
            $inner = trim(strip_tags($m[1]));
            if ($inner === '') {
                return '';
            }
            if (preg_match('#^https?://#i', $inner) || preg_match('#^www\.#i', $inner)) {
                return '';
            }

            return $inner;
        }, $html);

        return is_string($result) ? $result : $html;
    }

    private function removeBareUrls(string $text): string
    {
        $text = preg_replace('#https?://[^\s<>"\']+#iu', '', $text) ?? $text;
        $text = preg_replace('#www\.[^\s<>"\']+#iu', '', $text) ?? $text;

        return $text;
    }

    private function removeSourceReferences(string $text, ?string $sourceUrl): string
    {
        $text = preg_replace('/\bSource:\s*https?\S+/iu', '', $text) ?? $text;
        $text = preg_replace('/\bImported from\s*[^\n.]+/iu', '', $text) ?? $text;

        if ($sourceUrl) {
            $host = parse_url($sourceUrl, PHP_URL_HOST);
            if (is_string($host) && $host !== '') {
                $hostPattern = preg_quote($host, '/');
                $text = preg_replace('/'.$hostPattern.'/iu', '', $text) ?? $text;

                $baseName = Str::before(strtolower($host), '.');
                if (strlen($baseName) >= 4) {
                    $text = preg_replace('/\b'.preg_quote($baseName, '/').'\b/iu', '', $text) ?? $text;
                }
            }
        }

        return $text;
    }
}
