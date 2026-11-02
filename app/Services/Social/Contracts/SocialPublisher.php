<?php

namespace App\Services\Social\Contracts;

use App\Models\ScheduledPost;

interface SocialPublisher
{
    public function platform(): string;

    public function publish(ScheduledPost $post): void;
}
