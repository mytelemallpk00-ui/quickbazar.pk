<?php

namespace App\Services\Social;

use App\Models\ScheduledPost;
use App\Services\Social\Contracts\SocialPublisher;
use App\Support\Social\SocialPlatforms;

class SocialPostPublisher
{
    /** @param  array<string, SocialPublisher>  $drivers */
    public function __construct(
        protected array $drivers,
    ) {}

    public function publish(ScheduledPost $post): void
    {
        $errors = [];
        foreach ($post->platforms ?? [] as $platform) {
            $platform = strtolower((string) $platform);
            if (! in_array($platform, SocialPlatforms::schedulable(), true)) {
                $errors[] = SocialPlatforms::label($platform).': auto-publish not enabled for this channel';

                continue;
            }

            try {
                $this->driver($platform)->publish($post);
            } catch (\Throwable $e) {
                $errors[] = SocialPlatforms::label($platform).': '.$e->getMessage();
            }
        }

        if ($errors !== []) {
            $post->markFailed(implode("\n", $errors));

            return;
        }

        $post->markPublished();
    }

    protected function driver(string $platform): SocialPublisher
    {
        if (! isset($this->drivers[$platform])) {
            throw new \RuntimeException("No publisher registered for {$platform}.");
        }

        return $this->drivers[$platform];
    }
}
