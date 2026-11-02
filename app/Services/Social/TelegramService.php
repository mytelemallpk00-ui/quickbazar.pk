<?php

namespace App\Services\Social;

use App\Models\ScheduledPost;
use App\Services\Social\Concerns\InteractsWithSocialCredentials;
use App\Services\Social\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class TelegramService implements SocialPublisher
{
    use InteractsWithSocialCredentials;

    public function platform(): string
    {
        return 'telegram';
    }

    public function publish(ScheduledPost $post): void
    {
        $token = $this->ensureCredential('bot_token', 'Telegram bot token');
        $chatId = $this->ensureCredential('chat_id', 'Telegram chat ID');
        $text = implode("\n\n", array_filter([$post->title, $post->caption]));
        $mediaUrl = $post->publicMediaUrl();

        if ($mediaUrl) {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendPhoto", [
                'chat_id' => $chatId,
                'photo' => $mediaUrl,
                'caption' => \Illuminate\Support\Str::limit($text, 1024, ''),
            ]);
        } else {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'disable_web_page_preview' => false,
            ]);
        }

        if (! $response->successful() || ! data_get($response->json(), 'ok')) {
            $message = data_get($response->json(), 'description');

            throw new \RuntimeException(is_string($message) && $message !== '' ? $message : \Illuminate\Support\Str::limit($response->body(), 200));
        }
    }
}
