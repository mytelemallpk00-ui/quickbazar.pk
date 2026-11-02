<?php

namespace App\Services\Social;

use App\Models\ScheduledPost;
use App\Services\Social\Concerns\InteractsWithSocialCredentials;
use App\Services\Social\Contracts\SocialPublisher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class YouTubeService implements SocialPublisher
{
    use InteractsWithSocialCredentials;

    public function platform(): string
    {
        return 'youtube';
    }

    public function publish(ScheduledPost $post): void
    {
        $accessToken = $this->resolveAccessToken();
        $mediaPath = ltrim((string) ($post->media_path ?? ''), '/');

        if ($mediaPath === '' || ! Storage::disk('public')->exists($mediaPath)) {
            throw new \RuntimeException('YouTube posting requires a video file in media_path (public disk).');
        }

        $binary = Storage::disk('public')->get($mediaPath);
        $title = filled($post->title) ? (string) $post->title : \Illuminate\Support\Str::limit(strip_tags($post->caption), 90, '…');
        $description = (string) $post->caption;

        $metadata = [
            'snippet' => [
                'title' => $title,
                'description' => $description,
                'categoryId' => '22',
            ],
            'status' => [
                'privacyStatus' => 'public',
                'selfDeclaredMadeForKids' => false,
            ],
        ];

        $boundary = 'beautage_yt_'.uniqid();
        $body = $this->multipartBody($boundary, $metadata, $binary, basename($mediaPath));

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'multipart/related; boundary='.$boundary])
            ->withBody($body, 'multipart/related; boundary='.$boundary)
            ->post('https://www.googleapis.com/upload/youtube/v3/videos?part=snippet,status&uploadType=multipart');

        if (! $response->successful()) {
            $message = data_get($response->json(), 'error.message');

            throw new \RuntimeException(is_string($message) && $message !== '' ? $message : \Illuminate\Support\Str::limit($response->body(), 200));
        }
    }

    protected function resolveAccessToken(): string
    {
        $direct = $this->cred('access_token');
        if (filled($direct)) {
            return $direct;
        }

        $refresh = $this->ensureCredential('refresh_token', 'YouTube refresh token');
        $clientId = $this->ensureCredential('client_id', 'YouTube client ID');
        $clientSecret = $this->ensureCredential('client_secret', 'YouTube client secret');

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refresh,
            'grant_type' => 'refresh_token',
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('YouTube token refresh failed.');
        }

        $token = data_get($response->json(), 'access_token');
        if (! is_string($token) || $token === '') {
            throw new \RuntimeException('YouTube access token missing after refresh.');
        }

        return $token;
    }

    /** @param  array<string, mixed>  $metadata */
    protected function multipartBody(string $boundary, array $metadata, string $binary, string $filename): string
    {
        $metaJson = json_encode($metadata, JSON_THROW_ON_ERROR);
        $eol = "\r\n";

        return '--'.$boundary.$eol
            .'Content-Type: application/json; charset=UTF-8'.$eol.$eol
            .$metaJson.$eol
            .'--'.$boundary.$eol
            .'Content-Type: application/octet-stream'.$eol.$eol
            .$binary.$eol
            .'--'.$boundary.'--'.$eol;
    }
}
