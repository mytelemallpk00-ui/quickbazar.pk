<?php

namespace App\Services\Social;

use App\Models\ScheduledPost;
use App\Services\Social\Concerns\InteractsWithSocialCredentials;
use App\Services\Social\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;

class LinkedInService implements SocialPublisher
{
    use InteractsWithSocialCredentials;

    public function platform(): string
    {
        return 'linkedin';
    }

    public function publish(ScheduledPost $post): void
    {
        $token = $this->ensureCredential('access_token', 'LinkedIn access token');
        $author = $this->ensureCredential('organization_urn', 'LinkedIn organization URN');

        $text = implode("\n\n", array_filter([$post->title, $post->caption]));

        $payload = [
            'author' => $author,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $text,
                    ],
                    'shareMediaCategory' => 'NONE',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        $response = Http::withToken($token)
            ->withHeaders([
                'X-Restli-Protocol-Version' => '2.0.0',
                'LinkedIn-Version' => '202405',
            ])
            ->post('https://api.linkedin.com/v2/ugcPosts', $payload);

        if (! $response->successful()) {
            $message = data_get($response->json(), 'message');

            throw new \RuntimeException(is_string($message) && $message !== '' ? $message : \Illuminate\Support\Str::limit($response->body(), 200));
        }
    }
}
