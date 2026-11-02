<?php

namespace App\Services\GoogleIndex;

use App\Models\GoogleIndexLog;
use App\Models\User;
use App\Support\GoogleIndexCredentialStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleIndexService
{
    private ?string $accessToken = null;

    public function isConfigured(): bool
    {
        return GoogleIndexCredentialStore::isConfigured();
    }

    /**
     * @return array{success: bool, log: GoogleIndexLog, message: string}
     */
    public function publish(
        string $url,
        string $action = GoogleIndexLog::ACTION_UPDATED,
        ?Model $indexable = null,
        ?User $user = null,
    ): array {
        $url = GoogleIndexUrl::normalize($url);
        if ($url === '') {
            throw new GoogleIndexException('URL is required.');
        }

        if (! in_array($action, [GoogleIndexLog::ACTION_UPDATED, GoogleIndexLog::ACTION_DELETED], true)) {
            throw new GoogleIndexException('Invalid indexing action.');
        }

        if (! $this->isConfigured()) {
            throw GoogleIndexException::missingServiceAccount();
        }

        try {
            $response = Http::withToken($this->accessToken())
                ->acceptJson()
                ->timeout(30)
                ->post((string) config('google-indexing.publish_url'), [
                    'url' => $url,
                    'type' => $action,
                ]);

            $body = $response->json();
            $message = $this->humanMessage($response->status(), $body, (string) $response->body());

            $log = GoogleIndexLog::query()->create([
                'url' => $url,
                'action' => $action,
                'status' => $response->successful() ? GoogleIndexLog::STATUS_SUCCESS : GoogleIndexLog::STATUS_FAILED,
                'http_status' => $response->status(),
                'message' => $message,
                'response_body' => is_array($body) ? $body : ['raw' => Str::limit((string) $response->body(), 2000)],
                'indexable_type' => $indexable?->getMorphClass(),
                'indexable_id' => $indexable?->getKey(),
                'user_id' => $user?->id,
            ]);

            if (! $response->successful()) {
                throw GoogleIndexException::fromApi($response->status(), $message);
            }

            return [
                'success' => true,
                'log' => $log,
                'message' => $message,
            ];
        } catch (GoogleIndexException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $log = GoogleIndexLog::query()->create([
                'url' => $url,
                'action' => $action,
                'status' => GoogleIndexLog::STATUS_FAILED,
                'http_status' => null,
                'message' => Str::limit($e->getMessage(), 500),
                'response_body' => null,
                'indexable_type' => $indexable?->getMorphClass(),
                'indexable_id' => $indexable?->getKey(),
                'user_id' => $user?->id,
            ]);

            throw new GoogleIndexException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function latestLogForUrl(string $url): ?GoogleIndexLog
    {
        return GoogleIndexLog::query()
            ->where('url', GoogleIndexUrl::normalize($url))
            ->latest('id')
            ->first();
    }

    private function accessToken(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $account = GoogleIndexCredentialStore::serviceAccount();
        if ($account === null) {
            throw GoogleIndexException::missingServiceAccount();
        }

        $jwt = $this->buildJwt($account);
        $response = Http::asForm()
            ->timeout(20)
            ->post((string) config('google-indexing.token_url'), [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

        if (! $response->successful()) {
            $msg = (string) ($response->json('error_description') ?? $response->json('error') ?? $response->body());
            throw GoogleIndexException::fromApi($response->status(), Str::limit($msg, 300));
        }

        $token = $response->json('access_token');
        if (! is_string($token) || $token === '') {
            throw new GoogleIndexException('Google OAuth returned no access token.');
        }

        $this->accessToken = $token;

        return $this->accessToken;
    }

    /** @param  array<string, mixed>  $account */
    private function buildJwt(array $account): string
    {
        $now = time();
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $account['client_email'],
            'scope' => config('google-indexing.scope'),
            'aud' => config('google-indexing.token_url'),
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_THROW_ON_ERROR));

        $input = $header.'.'.$claims;
        $privateKey = openssl_pkey_get_private((string) $account['private_key']);
        if ($privateKey === false) {
            throw new GoogleIndexException('Invalid private key in service account JSON.');
        }

        $signature = '';
        if (! openssl_sign($input, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new GoogleIndexException('Could not sign Google Indexing JWT.');
        }

        return $input.'.'.$this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /** @param  array<string, mixed>|null  $body */
    private function humanMessage(int $status, ?array $body, string $raw): string
    {
        $error = $body['error']['message'] ?? $body['error']['status'] ?? null;
        if (is_string($error) && $error !== '') {
            if ($status === 429 || str_contains(strtolower($error), 'quota')) {
                return 'Daily quota reached — Google allows a limited number of indexing requests per day. Try again tomorrow.';
            }

            return Str::limit($error, 400);
        }

        if ($status === 403) {
            return 'Permission denied — add the service account as Owner in Google Search Console for this property.';
        }

        if ($status >= 200 && $status < 300) {
            return 'URL notification sent to Google Indexing API successfully.';
        }

        return Str::limit($raw !== '' ? $raw : 'Unexpected Google Indexing API response.', 400);
    }
}
