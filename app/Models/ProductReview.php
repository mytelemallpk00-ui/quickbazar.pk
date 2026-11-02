<?php

namespace App\Models;

use App\Support\SiteSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'author_name',
        'author_email',
        'rating',
        'title',
        'body',
        'is_verified_buyer',
        'is_published',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified_buyer' => 'boolean',
            'is_published' => 'boolean',
            'photos' => 'array',
        ];
    }

    /** @return array<int, string> */
    public function photoUrls(): array
    {
        return SiteSettings::reviewPhotoUrls($this->photos);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function authorInitials(): string
    {
        $parts = preg_split('/\s+/', trim($this->author_name)) ?: [];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials ?: '?';
    }

    protected static function booted(): void
    {
        $refresh = function (ProductReview $review): void {
            $review->product?->refreshReviewAggregates();
        };

        static::saved($refresh);
        static::deleted($refresh);
    }
}
