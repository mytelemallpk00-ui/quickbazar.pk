<?php

namespace App\Services\Newsletter;

use App\Jobs\SendNewsletterBroadcastJob;
use App\Mail\PartnerIrNewsletterMail;
use App\Mail\StoreNewsletterMail;
use App\Models\CmsPage;
use App\Models\NewsletterBroadcast;
use App\Models\NewsletterSubscriber;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NewsletterBroadcastService
{
    /** @param  array<string, mixed>  $payload */
    public function queueStoreBroadcast(User $author, array $payload): NewsletterBroadcast
    {
        return $this->queueBroadcast(NewsletterSubscriber::LIST_STORE, $author, $payload);
    }

    /** @param  array<string, mixed>  $payload */
    public function queuePartnerBroadcast(User $author, array $payload): NewsletterBroadcast
    {
        return $this->queueBroadcast(NewsletterSubscriber::LIST_PARTNER, $author, $payload);
    }

    /** @param  array<string, mixed>  $payload */
    public function queueBroadcast(string $list, User $author, array $payload): NewsletterBroadcast
    {
        $recipients = NewsletterSubscriber::query()->where('list', $list)->count();

        $broadcast = NewsletterBroadcast::query()->create([
            'list' => $list,
            'subject' => (string) ($payload['subject'] ?? 'Beautage update'),
            'preheader' => $payload['preheader'] ?? null,
            'headline' => $payload['headline'] ?? null,
            'body_html' => $payload['body_html'] ?? null,
            'payload' => $payload,
            'recipients_count' => $recipients,
            'status' => NewsletterBroadcast::STATUS_PENDING,
            'created_by' => $author->id,
        ]);

        SendNewsletterBroadcastJob::dispatchSync($broadcast);

        return $broadcast->fresh() ?? $broadcast;
    }

    public function sendBroadcast(NewsletterBroadcast $broadcast): void
    {
        $broadcast->update(['status' => NewsletterBroadcast::STATUS_SENDING]);

        $subscribers = NewsletterSubscriber::query()
            ->where('list', $broadcast->list)
            ->orderBy('id')
            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            try {
                if ($broadcast->list === NewsletterSubscriber::LIST_STORE) {
                    Mail::to($subscriber->email)->send(new StoreNewsletterMail($broadcast, $subscriber->email));
                } else {
                    Mail::to($subscriber->email)->send(new PartnerIrNewsletterMail($broadcast, $subscriber->email));
                }
                $sent++;
            } catch (\Throwable) {
                $failed++;
            }
        }

        $broadcast->update([
            'sent_count' => $sent,
            'failed_count' => $failed,
            'status' => $failed > 0 && $sent === 0
                ? NewsletterBroadcast::STATUS_FAILED
                : NewsletterBroadcast::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /** @return array<string, mixed> */
    public function buildPartnerPayloadFromCmsPage(CmsPage $page): array
    {
        $excerpt = Str::limit(strip_tags((string) $page->hero_subtitle), 280);

        return [
            'subject' => 'Partner update: '.$page->title,
            'preheader' => $excerpt,
            'headline' => $page->hero_title ?: $page->title,
            'body_html' => '<p>'.$excerpt.'</p>',
            'cms_slug' => $page->slug,
            'cms_page_type' => $page->page_type,
            'cta_label' => 'Read full update',
            'cta_url' => Route::has($page->slug)
                ? route($page->slug, absolute: true)
                : url('/'.$page->slug),
        ];
    }

    /** @param  array<string, mixed>  $payload */
    public function sendPartnerAutoFromCmsPage(CmsPage $page, ?User $author = null): ?NewsletterBroadcast
    {
        $author ??= auth()->user();
        if (! $author) {
            return null;
        }

        $count = NewsletterSubscriber::query()->where('list', NewsletterSubscriber::LIST_PARTNER)->count();
        if ($count === 0) {
            return null;
        }

        $payload = $this->buildPartnerPayloadFromCmsPage($page);
        if (filled($page->content_html)) {
            $payload['body_html'] = Str::limit(strip_tags((string) $page->content_html), 1200);
        }

        return $this->queuePartnerBroadcast($author, $payload);
    }

    /** @return array<int, array<string, mixed>> */
    public function storeProductCards(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        $order = array_flip(array_map('intval', $productIds));

        return Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->sortBy(fn (Product $product) => $order[$product->id] ?? 999)
            ->values()
            ->map(fn (Product $product) => [
                'name' => $product->name,
                'price' => $product->price,
                'old_price' => $product->old_price,
                'badge' => $product->badge,
                'url' => route('product.show', $product, absolute: true),
                'image_url' => $this->absoluteAssetUrl($product->image),
            ])
            ->all();
    }

    /** @return array<string, mixed>|null */
    public function activeBundleHighlight(): ?array
    {
        $bundle = ProductBundle::query()
            ->with('hostProduct')
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if (! $bundle || ! $bundle->hostProduct) {
            return null;
        }

        $host = $bundle->hostProduct;

        return [
            'title' => $host->name,
            'subtitle' => $bundle->subtitle,
            'badge' => $bundle->badge_label,
            'url' => route('product.show', $host, absolute: true),
        ];
    }

    public function absoluteAssetUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url('/'.ltrim($path, '/'));
    }
}
