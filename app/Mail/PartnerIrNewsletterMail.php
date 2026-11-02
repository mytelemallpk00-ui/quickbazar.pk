<?php

namespace App\Mail;

use App\Models\NewsletterBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerIrNewsletterMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public NewsletterBroadcast $broadcast,
        public string $recipientEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->broadcast->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.beautage-partner-ir-newsletter',
            with: [
                'broadcast' => $this->broadcast,
                'payload' => $this->broadcast->payload ?? [],
                'recipientEmail' => $this->recipientEmail,
            ],
        );
    }
}
