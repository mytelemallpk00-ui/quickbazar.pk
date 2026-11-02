<?php

namespace App\Services\Social;

use App\Models\ScheduledPost;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SocialScheduleBuilder
{
    public function __construct(
        protected SocialContentGenerator $generator,
    ) {}

    /**
     * @param  array{
     *   category: string,
     *   post_limit: int,
     *   platforms: list<string>,
     *   start_at: string,
     *   end_at: string,
     *   interval_hours: int,
     *   media_path?: ?string,
     * }  $input
     */
    public function createQueue(array $input): int
    {
        $category = trim((string) $input['category']);
        $limit = max(1, min(50, (int) ($input['post_limit'] ?? 1)));
        $platforms = array_values(array_filter($input['platforms'] ?? []));
        if ($category === '' || $platforms === []) {
            throw new \InvalidArgumentException('Category and at least one platform are required.');
        }

        $start = Carbon::parse($input['start_at'])->startOfMinute();
        $end = Carbon::parse($input['end_at'])->endOfMinute();
        if ($end->lessThan($start)) {
            throw new \InvalidArgumentException('End date must be after start date.');
        }

        $intervalHours = max(1, (int) ($input['interval_hours'] ?? 24));
        $slots = $this->buildScheduleSlots($start, $end, $limit, $intervalHours);
        $contents = $this->generator->generate($category, $limit);
        $mediaPath = filled($input['media_path'] ?? null) ? (string) $input['media_path'] : null;

        $created = 0;
        foreach ($contents as $index => $content) {
            ScheduledPost::query()->create([
                'category' => $category,
                'platforms' => $platforms,
                'title' => $content['title'] ?? null,
                'caption' => $content['caption'],
                'media_path' => $mediaPath,
                'status' => ScheduledPost::STATUS_PENDING,
                'scheduled_at' => $slots[$index] ?? $start->copy()->addHours($index * $intervalHours),
            ]);
            $created++;
        }

        return $created;
    }

    /** @return Collection<int, Carbon> */
    protected function buildScheduleSlots(Carbon $start, Carbon $end, int $count, int $intervalHours): Collection
    {
        $slots = collect();
        $cursor = $start->copy();

        for ($i = 0; $i < $count; $i++) {
            if ($cursor->greaterThan($end)) {
                $cursor = $end->copy()->subMinutes(min(30, $i * 5));
            }
            $slots->push($cursor->copy());
            $cursor->addHours($intervalHours);
        }

        return $slots;
    }
}
