<?php

namespace App\Mail;

use App\Models\NewsletterBroadcast;
use App\Services\Newsletter\NewsletterBroadcastService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreNewsletterMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** @var array<int, array<string, mixed>> */
    public array $products;

    /** @var array<string, mixed>|null */
    public ?array $bundleHighlight;

    public function __construct(
        public NewsletterBroadcast $broadcast,
        public string $recipientEmail,
    ) {
        $service = app(NewsletterBroadcastService::class);
        $payload = $broadcast->payload ?? [];
        $this->products = $service->storeProductCards($payload['product_ids'] ?? []);
        $this->bundleHighlight = $payload['bundle_highlight'] ?? $service->activeBundleHighlight();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->broadcast->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.beautage-store-newsletter',
            with: [
                'broadcast' => $this->broadcast,
                'payload' => $this->broadcast->payload ?? [],
                'products' => $this->products,
                'bundleHighlight' => $this->bundleHighlight,
                'recipientEmail' => $this->recipientEmail,
            ],
        );
    }
}
