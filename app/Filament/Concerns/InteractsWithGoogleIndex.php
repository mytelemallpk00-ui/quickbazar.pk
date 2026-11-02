<?php

namespace App\Filament\Concerns;

use App\Models\GoogleIndexLog;
use App\Models\Product;
use App\Services\GoogleIndex\ClaudeSeoPreflightService;
use App\Services\GoogleIndex\GoogleIndexException;
use App\Services\GoogleIndex\GoogleIndexService;
use App\Services\GoogleIndex\GoogleIndexUrl;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

trait InteractsWithGoogleIndex
{
    protected function notifyGoogleIndexSuccess(string $message): void
    {
        Notification::make()
            ->title('Sent to Google Indexing API')
            ->body($message)
            ->success()
            ->send();
    }

    protected function notifyGoogleIndexFailure(string $message): void
    {
        Notification::make()
            ->title('Google indexing failed')
            ->body($message)
            ->danger()
            ->duration(12000)
            ->send();
    }

    protected function pushProductToGoogle(Product $product, string $action = 'URL_UPDATED'): void
    {
        try {
            $result = app(GoogleIndexService::class)->publish(
                GoogleIndexUrl::forProduct($product),
                $action,
                $product,
                auth()->user(),
            );
            $this->notifyGoogleIndexSuccess($result['message']);
        } catch (GoogleIndexException $e) {
            $this->notifyGoogleIndexFailure($e->getMessage());
        }
    }

    protected function pushUrlToGoogle(string $url, string $action = 'URL_UPDATED'): void
    {
        try {
            $result = app(GoogleIndexService::class)->publish(
                $url,
                $action,
                null,
                auth()->user(),
            );
            $this->notifyGoogleIndexSuccess($result['message']);
        } catch (GoogleIndexException $e) {
            $this->notifyGoogleIndexFailure($e->getMessage());
        }
    }

    /** @return array{score: int, ready: bool, summary: string, suggestions: list<string>}|null */
    /**
     * @param  Collection<int, Product>|iterable<int, Product>  $products
     * @return array{success: int, failed: int, skipped: int, stopped_reason: ?string}
     */
    protected function bulkPushProductsToGoogle(
        iterable $products,
        string $action = GoogleIndexLog::ACTION_UPDATED,
        int $delayMs = 300,
    ): array {
        $service = app(GoogleIndexService::class);
        $user = auth()->user();
        $success = 0;
        $failed = 0;
        $skipped = 0;
        $stoppedReason = null;

        foreach ($products as $product) {
            if (! $product instanceof Product) {
                $skipped++;

                continue;
            }

            try {
                $service->publish(
                    GoogleIndexUrl::forProduct($product),
                    $action,
                    $product,
                    $user,
                );
                $success++;
            } catch (GoogleIndexException $e) {
                $failed++;
                if ($this->isGoogleIndexQuotaError($e)) {
                    $stoppedReason = $e->getMessage();
                    break;
                }
            }

            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'skipped' => $skipped,
            'stopped_reason' => $stoppedReason,
        ];
    }

    protected function isGoogleIndexQuotaError(GoogleIndexException $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'quota')
            || str_contains($message, '429')
            || str_contains($message, 'rate');
    }

    /** @param array{success: int, failed: int, skipped: int, stopped_reason: ?string} $summary */
    protected function notifyGoogleIndexBulkSummary(array $summary): void
    {
        $body = "Success: {$summary['success']} · Failed: {$summary['failed']}";
        if ($summary['skipped'] > 0) {
            $body .= " · Skipped: {$summary['skipped']}";
        }
        if (filled($summary['stopped_reason'] ?? null)) {
            $body .= '. Stopped early: '.$summary['stopped_reason'];
        }

        Notification::make()
            ->title('Bulk indexing finished')
            ->body($body)
            ->{$summary['failed'] === 0 && blank($summary['stopped_reason']) ? 'success' : 'warning'}()
            ->duration(15000)
            ->send();
    }

    protected function analyzeProductSeoWithClaude(Product $product): ?array
    {
        try {
            return app(ClaudeSeoPreflightService::class)->analyze([
                'url' => GoogleIndexUrl::forProduct($product),
                'title' => $product->meta_title ?: $product->name,
                'description' => $product->meta_description ?: $product->short_description ?: strip_tags((string) $product->description),
                'focus_keyword' => $product->seo_focus_keyword,
            ]);
        } catch (GoogleIndexException $e) {
            $this->notifyGoogleIndexFailure($e->getMessage());

            return null;
        }
    }
}
