<?php

namespace App\Console\Commands;

use App\Jobs\PublishScheduledPostJob;
use App\Models\ScheduledPost;
use Illuminate\Console\Command;

class PublishSocialPostsCommand extends Command
{
    protected $signature = 'social:publish-posts {--sync : Publish due posts in-process instead of queue}';

    protected $description = 'Dispatch publishing jobs for scheduled social posts that are due';

    public function handle(): int
    {
        $due = ScheduledPost::query()->pendingDue()->orderBy('scheduled_at')->get();

        if ($due->isEmpty()) {
            $this->info('No due social posts.');

            return self::SUCCESS;
        }

        foreach ($due as $post) {
            if ($this->option('sync')) {
                PublishScheduledPostJob::dispatchSync($post);
            } else {
                PublishScheduledPostJob::dispatch($post);
            }
            $this->line("Queued post #{$post->id} ({$post->category})");
        }

        $this->info("Processed {$due->count()} due post(s).");

        return self::SUCCESS;
    }
}
