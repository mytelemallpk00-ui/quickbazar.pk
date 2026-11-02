<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'category',
        'read_time',
        'published_at',
        'image',
        'author',
        'author_role',
        'author_avatar',
        'likes',
        'snippet',
        'content',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return array<int, array<string, mixed>> */
    public static function journalForJs(): array
    {
        return static::query()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (BlogPost $post) => $post->toJournalArray())
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    public function toJournalArray(): array
    {
        return [
            'id' => $this->is_featured ? 'featured' : $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category,
            'readTime' => $this->read_time,
            'date' => $this->published_at->format('F j, Y'),
            'image' => $this->image,
            'author' => $this->author,
            'authorRole' => $this->author_role,
            'authorAvatar' => $this->author_avatar,
            'likes' => $this->likes,
            'snippet' => $this->snippet,
            'content' => $this->content,
        ];
    }
}
