<?php

namespace App\Services\Social;

use App\Models\ScheduledPost;
use App\Services\Social\Concerns\InteractsWithSocialCredentials;
use App\Services\Social\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class InstagramService implements SocialPublisher
{
    use InteractsWithSocialCredentials;

    public function platform(): string
    {
        return 'instagram';
    }

    public function publish(ScheduledPost $post): void
    {
        $token = $this->ensureCredential('access_token', 'Instagram access token');
        $accountId = $this->ensureCredential('business_account_id', 'Instagram business account ID');
        $mediaUrl = $post->publicMediaUrl();

        if (! $mediaUrl) {
            throw new \RuntimeException('Instagram requires an image or video (media_path).');
        }

        $caption = $this->composeCaption($post);

        $container = Http::asForm()->post("https://graph.facebook.com/v21.0/{$accountId}/media", [
            'image_url' => $mediaUrl,
            'caption' => $caption,
            'access_token' => $token,
        ]);

        if (! $container->successful()) {
            throw new \RuntimeException($this->extractError($container->json(), $container->body()));
        }

        $creationId = data_get($container->json(), 'id');
        if (! filled($creationId)) {
            throw new \RuntimeException('Instagram media container ID missing.');
        }

        $publish = Http::asForm()->post("https://graph.facebook.com/v21.0/{$accountId}/media_publish", [
            'creation_id' => $creationId,
            'access_token' => $token,
        ]);

        if (! $publish->successful()) {
            throw new \RuntimeException($this->extractError($publish->json(), $publish->body()));
        }
    }

    protected function composeCaption(ScheduledPost $post): string
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
