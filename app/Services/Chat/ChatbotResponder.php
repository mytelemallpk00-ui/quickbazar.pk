<?php

namespace App\Services\Chat;

class ChatbotResponder
{
    public function __construct(
        protected SimpleSupportChatbot $bot,
    ) {}

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>, whatsapp_url: ?string} */
    public function replyFor(string $customerMessage): array
    {
        return $this->bot->replyFor($customerMessage);
    }
}
