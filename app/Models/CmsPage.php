<?php

namespace App\Models;

use App\Models\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasSeoMeta;

    public const TYPE_GENERIC = 'generic';

    public const TYPE_SIZE_GUIDE = 'size-guide';

    public const TYPE_CAREERS = 'careers';

    public const TYPE_PRESS_RELEASES = 'press-releases';

    public const TYPE_FAQ = 'faq';

    public const TYPE_EVENTS = 'events';

    protected $fillable = [
        'slug',
        'title',
        'page_type',
        'hero_badge',
        'hero_title',
        'hero_subtitle',
        'hero_stat_value',
        'hero_stat_label',
        'hero_buttons',
        'content_html',
        'sections',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'hero_buttons' => 'array',
            'sections' => 'array',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (CmsPage $page): void {
            if (blank($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function viewName(): string
    {
        return match ($this->page_type) {
            self::TYPE_SIZE_GUIDE => 'pages.cms.size-guide',
            self::TYPE_CAREERS => 'pages.cms.careers',
            default => 'pages.cms.generic',
        };
    }

    /** @return array<string, string> */
    public static function pageTypeLabels(): array
    {
        return [
            self::TYPE_GENERIC => 'General page',
            self::TYPE_PRESS_RELEASES => 'Press releases',
            self::TYPE_FAQ => 'FAQ',
            self::TYPE_EVENTS => 'Events',
            self::TYPE_SIZE_GUIDE => 'Size guide',
            self::TYPE_CAREERS => 'Careers',
        ];
    }

    public static function defaultPage(string $slug): ?self
    {
        return match ($slug) {
            'press-releases' => new self([
                'slug' => 'press-releases',
                'title' => 'Press Releases',
                'page_type' => self::TYPE_PRESS_RELEASES,
                'hero_badge' => 'MEDIA & ANNOUNCEMENTS',
                'hero_title' => 'Press Releases',
                'hero_subtitle' => 'Official Quick Bazar announcements, product launches, and corporate news for media and partners.',
                'content_html' => '<p>Manage this page from <strong>Admin → Content → Press Releases</strong>.</p>',
                'meta_title' => 'Press Releases',
                'is_published' => true,
            ]),
            'faq' => new self([
                'slug' => 'faq',
                'title' => 'FAQ',
                'page_type' => self::TYPE_FAQ,
                'hero_badge' => 'PARTNER SUPPORT',
                'hero_title' => 'Frequently Asked Questions',
                'hero_subtitle' => 'Answers for partners and wholesale collaborators.',
                'content_html' => '<p>Add FAQ content in the admin panel under <strong>Content → FAQ</strong>.</p>',
                'meta_title' => 'FAQ',
                'is_published' => true,
            ]),
            'events' => new self([
                'slug' => 'events',
                'title' => 'Events',
                'page_type' => self::TYPE_EVENTS,
                'hero_badge' => 'PARTNER EVENTS',
                'hero_title' => 'Events',
                'hero_subtitle' => 'Webinars, launch events, and partner roundtables.',
                'content_html' => '<p>Manage events content from <strong>Admin → Content → Events</strong>.</p>',
                'meta_title' => 'Events',
                'is_published' => true,
            ]),
            default => null,
        };
    }
}
