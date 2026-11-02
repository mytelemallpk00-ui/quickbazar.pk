<?php

namespace App\Services\Social;

use App\Models\ScheduledPost;
use App\Services\Social\Concerns\InteractsWithSocialCredentials;
use App\Services\Social\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class FacebookService implements SocialPublisher
{
    use InteractsWithSocialCredentials;

    public function platform(): string
    {
        return 'facebook';
    }

    public function publish(ScheduledPost $post): void
    {
        $token = $this->ensureCredential('access_token', 'Page access token');
        $pageId = $this->ensureCredential('page_id', 'Facebook Page ID');
        $message = $this->composeMessage($post);
        $mediaUrl = $post->publicMediaUrl();

        if ($mediaUrl) {
            $response = Http::asForm()->post("https://graph.facebook.com/v21.0/{$pageId}/photos", [
                'url' => $mediaUrl,
                'caption' => $message,
                'access_token' => $token,
            ]);
        } else {
            $response = Http::asForm()->post("https://graph.facebook.com/v21.0/{$pageId}/feed", [
                'message' => $message,
                'access_token' => $token,
            ]);
        }

        if (! $response->successful()) {
            throw new \RuntimeException($this->extractError($response->json(), $response->body()));
        }
    }

    protected function composeMessage(ScheduledPost $post): string
    {
        $parts = array_filter([$post->title, $post->caption]);

        return implode("\n\n", $parts);
    }

    /** @param  array<string, mixed>|null  $json */
    protected function extractError(?array $json, string $body): string
    {
        $message = data_get($json, 'error.message');

        return is_string($message) && $message !== '' ? $message : \Illuminate\Support\Str::limit($body, 200);
    }
}
