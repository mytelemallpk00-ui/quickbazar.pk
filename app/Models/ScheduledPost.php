<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ScheduledPost extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'category',
        'platforms',
        'title',
        'caption',
        'media_path',
        'status',
        'scheduled_at',
        'error_log',
    ];

    protected function casts(): array
    {
        return [
            'platforms' => 'array',
            'scheduled_at' => 'datetime',
        ];
    }

    /** @param  Builder<ScheduledPost>  $query */
    public function scopePendingDue(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PENDING)
            ->where('scheduled_at', '<=', now());
    }

    public function markPublished(): void
    {
        $this->forceFill([
            'status' => self::STATUS_PUBLISHED,
            'error_log' => null,
        ])->save();
    }

    public function markFailed(string $message): void
    {
        $this->forceFill([
            'status' => self::STATUS_FAILED,
            'error_log' => $message,
        ])->save();
    }

    public function publicMediaUrl(): ?string
    {
        if (blank($this->media_path)) {
            return null;
        }

        $path = ltrim((string) $this->media_path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return url('/storage/'.$path);
    }
}
