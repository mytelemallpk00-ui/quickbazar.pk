<?php

namespace App\Filament\Forms;

use App\Support\SiteSettings;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

class SeoSection
{
    public static function tab(
        ?string $fallbackTitleField = 'name',
        ?string $slugField = 'slug',
        ?string $descriptionFallbackField = 'description',
    ): Tab {
        return Tab::make('SEO & Google')
            ->icon('heroicon-o-magnifying-glass-circle')
            ->schema(static::fields($fallbackTitleField, $slugField, $descriptionFallbackField));
    }

    /** @return list<Forms\Components\Component> */
    public static function fields(
        ?string $fallbackTitleField = 'name',
        ?string $slugField = 'slug',
        ?string $descriptionFallbackField = 'description',
    ): array {
        return [
            static::googlePreview($fallbackTitleField, $slugField, $descriptionFallbackField),
            Forms\Components\Section::make('Search engines')
                ->schema([
                    Forms\Components\TextInput::make('seo_focus_keyword')->label('Focus keyword')->maxLength(80)->live(debounce: 400),
                    Forms\Components\TextInput::make('meta_title')->label('SEO title')->maxLength(70)->live(debounce: 400),
                    Forms\Components\Textarea::make('meta_description')->label('Meta description')->rows(3)->maxLength(320)->live(debounce: 400),
                    Forms\Components\TextInput::make('meta_keywords')->label('Meta keywords'),
                    Forms\Components\Select::make('robots')
                        ->options([
                            'index,follow' => 'Index, follow (default)',
                            'index,nofollow' => 'Index, nofollow',
                            'noindex,follow' => 'Noindex, follow',
                            'noindex,nofollow' => 'Noindex, nofollow',
                        ])
                        ->default('index,follow')
                        ->native(false),
                    Forms\Components\TextInput::make('canonical_url')->label('Canonical URL')->url()->live(debounce: 400),
                ])->columns(2),
            Forms\Components\Section::make('Open Graph')
                ->schema([
                    Forms\Components\TextInput::make('og_title')->label('OG title')->maxLength(70),
                    Forms\Components\Textarea::make('og_description')->label('OG description')->rows(2),
                    AdminImageUpload::make('og_image', AdminImageUpload::PRESET_OG)
                        ->label('OG share image')
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function googlePreview(
        ?string $fallbackTitleField = 'name',
        ?string $slugField = 'slug',
        ?string $descriptionFallbackField = 'description',
        ?string $basePath = null,
    ): Forms\Components\Placeholder {
        return Forms\Components\Placeholder::make('google_serp_preview')
            ->label('')
            ->content(function (Get $get, $record) use ($fallbackTitleField, $slugField, $descriptionFallbackField, $basePath): HtmlString {
                $siteName = class_exists(SiteSettings::class)
                    ? SiteSettings::get('site_name', 'Quick Bazar')
                    : 'Quick Bazar';

                $fallbackTitle = $fallbackTitleField ? $get($fallbackTitleField) : null;
                if (! $fallbackTitle && $record && $fallbackTitleField) {
                    $fallbackTitle = $record->{$fallbackTitleField} ?? null;
                }

                $title = $get('meta_title')
                    ?: ($fallbackTitle ? $fallbackTitle.' — '.$siteName : $siteName);

                $desc = $get('meta_description');
                if (! $desc && $descriptionFallbackField) {
                    $desc = $get($descriptionFallbackField);
                }
                $desc ??= 'Elevate Your Natural Elegance with Quick Bazar.';

                $slug = $slugField ? ($get($slugField) ?: 'press-releases') : 'press-releases';
                $url = rtrim(config('app.url'), '/').'/'.ltrim($basePath ?? $slug, '/');

                return new HtmlString(view('filament.forms.google-serp-preview', [
                    'title' => $title,
                    'description' => $desc,
                    'url' => $url,
                ])->render());
            });
    }
}
