<?php

namespace App\Jobs;

use App\Models\ScheduledPost;
use App\Services\Social\SocialPostPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishScheduledPostJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 180;

    public function __construct(
        public ScheduledPost $scheduledPost,
    ) {}

    public function handle(SocialPostPublisher $publisher): void
    {
        $post = $this->scheduledPost->fresh();
        if (! $post || $post->status !== ScheduledPost::STATUS_PENDING) {
            return;
        }

        $publisher->publish($post);
    }
}
