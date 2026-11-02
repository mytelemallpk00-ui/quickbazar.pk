<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreAutomationMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param  list<array<string, mixed>>  $cartItems */
    /** @param  list<array<string, mixed>>  $products */
    public function __construct(
        public string $type,
        public string $subjectLine = '',
        public string $badge,
        public string $headline,
        public string $bodyHtml,
        public string $ctaLabel,
        public string $ctaUrl,
        public array $cartItems = [],
        public array $products = [],
        public string $discountCode = '',
        public ?string $orderNumber = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.store-automation',
            with: [
                'automationType' => $this->type,
                'badge' => $this->badge,
                'headline' => $this->headline,
                'bodyHtml' => $this->bodyHtml,
                'ctaLabel' => $this->ctaLabel,
                'ctaUrl' => $this->ctaUrl,
                'cartItems' => $this->cartItems,
                'products' => $this->products,
                'discountCode' => $this->discountCode,
                'orderNumber' => $this->orderNumber,
                'subject' => $this->subjectLine,
            ],
        );
    }
}
