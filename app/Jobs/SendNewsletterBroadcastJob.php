<?php

namespace App\Jobs;

use App\Models\NewsletterBroadcast;
use App\Services\Newsletter\NewsletterBroadcastService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewsletterBroadcastJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(
        public NewsletterBroadcast $broadcast,
    ) {}

    public function handle(NewsletterBroadcastService $service): void
    {
        $record = $this->broadcast->fresh();
        if (! $record || $record->status === NewsletterBroadcast::STATUS_SENT) {
            return;
        }

        $service->sendBroadcast($record);
    }
}
