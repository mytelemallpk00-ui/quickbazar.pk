<?php

namespace App\Filament\Forms;

use App\Models\Product;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

class HomepageContentForm
{
    /** @return array<int, \Filament\Forms\Components\Component> */
    public static function schema(): array
    {
        return [
            Placeholder::make('homepage_help')
                ->label('')
                ->content(new HtmlString(
                    '<p class="text-sm text-gray-600 dark:text-gray-400">'
                    .'Edit all homepage sections: hero, promo cards, category strip, showcase, flash deals, aesthetics gallery, featured promo, trending header, and celebrity endorsements. '
                    .'Upload images where offered; link products and collection slugs for real storefront URLs.'
                    .'</p>'
                ))
                ->columnSpanFull(),
            Section::make('Top bar')
                ->description('The thin strip above the logo. These are the dummy lines from the storefront mock.')
                ->schema([
                    TextInput::make('homepage_content.top_bar.left')->label('Left message')->maxLength(160)->columnSpanFull(),
                    TextInput::make('homepage_content.top_bar.center')->label('Center message')->maxLength(160),
                    TextInput::make('homepage_content.top_bar.right')->label('Right message')->maxLength(160),
                    TextInput::make('homepage_content.top_bar.phone_label')->label('Phone label')->maxLength(40),
                    TextInput::make('homepage_content.top_bar.phone')->label('Phone number')->maxLength(40),
                    TextInput::make('homepage_content.top_bar.about_label')->label('About link label')->maxLength(40),
                    TextInput::make('homepage_content.top_bar.about_url')->label('About link URL')->maxLength(200),
                ])->columns(2),
            Section::make('Hero slider')
                ->description('Full-width slides. Add, remove, or reorder. The storefront cycles them automatically.')
                ->schema([
                    Repeater::make('homepage_content.slides')
                        ->schema([
                            TextInput::make('badge')->label('Badge')->maxLength(80),
                            TextInput::make('title')->label('Headline')->required()->maxLength(140)->columnSpanFull(),
                            TextInput::make('accent')->label('Second line')->maxLength(140)->columnSpanFull(),
                            Textarea::make('description')->rows(2)->columnSpanFull(),
                            Textarea::make('checks')->label('Tick points, one per line')->rows(4)->columnSpanFull(),
                            TextInput::make('button_label')->label('Button')->maxLength(40),
                            TextInput::make('button_url')->label('Button URL')->maxLength(200),
                            TextInput::make('price_line')->label('Price line')->maxLength(80),
                            TextInput::make('image_url')->label('Image URL')->columnSpanFull(),
                            TextInput::make('image_alt')->label('Image alt')->maxLength(120),
                        ])->columns(2)->collapsible()->columnSpanFull(),
                ]),
            Section::make('Homepage sections')
                ->description('Turn each block of the homepage on or off. The header, mega menu, and footer stay in place.')
                ->schema([
                    Toggle::make('homepage_content.layout.hero')->label('Hero')->default(true),
                    Toggle::make('homepage_content.layout.steps')->label('How to order')->default(true),
                    Toggle::make('homepage_content.layout.trust')->label('Trust row')->default(true),
                    Toggle::make('homepage_content.layout.categories')->label('Categories')->default(true),
                    Toggle::make('homepage_content.layout.trending')->label('Trending products')->default(true),
                    Toggle::make('homepage_content.layout.reviews')->label('Reviews')->default(true),
                    Toggle::make('homepage_content.layout.journal')->label('Journal')->default(true),
                    Toggle::make('homepage_content.layout.flash')->label('Flash deals')->default(true),
                ])
                ->columns(2),
            Section::make('Main hero banner')
                ->description('Purple banner: badge, headline, prices, CTA, and right-side product image.')
                ->schema([
                    Toggle::make('homepage_content.hero.enabled')
                        ->label('Show hero banner')
                        ->default(true),
                    ColorPicker::make('homepage_content.hero.background_from')
                        ->label('Gradient start')
                        ->helperText('Optional — leave empty for theme purple gradient.'),
                    ColorPicker::make('homepage_content.hero.background_to')
                        ->label('Gradient end'),
                    TextInput::make('homepage_content.hero.badge')
                        ->label('Badge text')
                        ->placeholder('NEW QUICK BAZAR TECH RELEASE')
                        ->maxLength(120),
                    TextInput::make('homepage_content.hero.title_line1')
                        ->label('Headline (first line)')
                        ->maxLength(120)
                        ->columnSpanFull(),
                    TextInput::make('homepage_content.hero.title_accent')
                        ->label('Headline accent (gradient line)')
                        ->maxLength(120)
                        ->columnSpanFull(),
                    Textarea::make('homepage_content.hero.description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('homepage_content.hero.price_label')
                        ->label('Price label')
                        ->placeholder('Special Intro Price'),
                    TextInput::make('homepage_content.hero.price_display')
                        ->label('Sale price (numbers only)')
                        ->helperText('Displayed as Rs. on the site.'),
                    TextInput::make('homepage_content.hero.compare_price_display')
                        ->label('Compare-at price (optional)'),
                    TextInput::make('homepage_content.hero.button_label')
                        ->label('Button label')
                        ->placeholder('Reserve Order'),
                    Select::make('homepage_content.hero.product_id')
                        ->label('Add to bag product')
                        ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->native(false)
                        ->placeholder('Optional — links to Collections if empty'),
                    AdminImageUpload::make('homepage_content.hero.image_url', AdminImageUpload::PRESET_HOME_HERO)
                        ->label('Hero product image')
                        ->columnSpanFull(),
                    TextInput::make('homepage_content.hero.image_alt')
                        ->label('Image alt text')
                        ->maxLength(160)
                        ->columnSpanFull(),
                ])->columns(2),
            Section::make('Side promo cards')
                ->description('Two cards on the right of the hero (desktop).')
                ->schema([
                    Repeater::make('homepage_content.promo_cards')
                        ->label('Promo cards')
                        ->maxItems(2)
                        ->schema([
                            Select::make('style')
                                ->label('Card style')
                                ->options([
                                    'light' => 'White card',
                                    'gradient' => 'Purple gradient',
                                ])
                                ->required()
                                ->native(false),
                            TextInput::make('badge')->label('Badge')->maxLength(60),
                            TextInput::make('title')->label('Title')->required()->maxLength(120),
                            Textarea::make('description')->label('Description')->rows(2)->columnSpanFull(),
                            TextInput::make('price_display')->label('Price (numbers only)'),
                            TextInput::make('button_label')->label('Button / link label'),
                            Select::make('button_style')
                                ->label('Button style')
                                ->options([
                                    'text' => 'Text link (Add To Bag)',
                                    'solid' => 'Solid button',
                                ])
                                ->native(false),
                            Select::make('product_id')
                                ->label('Add to bag product')
                                ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->native(false),
                            AdminImageUpload::make('image_url', AdminImageUpload::PRESET_HOME_HERO)
                                ->label('Card image (optional)')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
            Section::make('Trust & category strip')
                ->description('Centered text and category image tiles below the hero.')
                ->schema([
                    TextInput::make('homepage_content.category_intro.badge')
                        ->label('Top badge')
                        ->maxLength(200)
                        ->columnSpanFull(),
                    TextInput::make('homepage_content.category_intro.heading')
                        ->label('Section heading')
                        ->maxLength(200)
                        ->columnSpanFull(),
                    Textarea::make('homepage_content.category_intro.description')
                        ->label('Section description')
                        ->rows(3)
                        ->columnSpanFull(),
                    Repeater::make('homepage_content.category_intro.trust_badges')
                        ->label('Trust badges (icon + label)')
                        ->schema([
                            TextInput::make('icon')
                                ->label('Lucide icon name')
                                ->helperText('shield-check, rotate-ccw, truck, …')
                                ->required(),
                            TextInput::make('label')->label('Label')->required(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                    Repeater::make('homepage_content.category_tiles')
                        ->label('Category tiles')
                        ->schema([
                            Toggle::make('is_view_all')
                                ->label('"View all" tile (purple, no image)')
                                ->live(),
                            TextInput::make('label')->label('Label')->required(),
                            TextInput::make('filter_value')
                                ->label('Category filter (legacy JS label)')
                                ->helperText('Used if no collection slug — e.g. Skincare, Haircare, or all')
                                ->required(),
                            TextInput::make('collection_slug')
                                ->label('Collection slug (link)')
                                ->helperText('Optional — matches category slug in Collections, e.g. skincare')
                                ->maxLength(80),
                            AdminImageUpload::make('image_url', AdminImageUpload::PRESET_HOME_CATEGORY_TILE)
                                ->label('Tile image')
                                ->hidden(fn (Get $get): bool => (bool) $get('is_view_all'))
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
            Section::make('Category showcase header')
                ->description('Tabs below use live categories from the catalog; edit titles here.')
                ->schema([
                    TextInput::make('homepage_content.showcase.badge')->label('Badge')->maxLength(120),
                    TextInput::make('homepage_content.showcase.heading')->label('Heading')->maxLength(200)->columnSpanFull(),
                    Textarea::make('homepage_content.showcase.description')->label('Description')->rows(2)->columnSpanFull(),
                ])->columns(2),
            Section::make('Flash deals')
                ->description('Spotlight card and section copy above the flash product grid.')
                ->schema([
                    TextInput::make('homepage_content.flash_deals.badge')->label('Badge')->maxLength(120)->columnSpanFull(),
                    TextInput::make('homepage_content.flash_deals.title')->label('Title')->maxLength(120),
                    TextInput::make('homepage_content.flash_deals.title_badge')->label('Title badge')->maxLength(80),
                    Textarea::make('homepage_content.flash_deals.description')->label('Description')->rows(3)->columnSpanFull(),
                    Select::make('homepage_content.flash_deals.spotlight_product_id')
                        ->label('Spotlight product')
                        ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->native(false)
                        ->placeholder('First flash product if empty'),
                    TextInput::make('homepage_content.flash_deals.spotlight_badge')->label('Spotlight badge')->maxLength(60),
                ])->columns(2),
            Section::make('Aesthetics gallery')
                ->description('Four image cards linking to collections.')
                ->schema([
                    TextInput::make('homepage_content.aesthetics_gallery.badge')->label('Badge')->maxLength(120),
                    TextInput::make('homepage_content.aesthetics_gallery.heading')->label('Heading')->maxLength(200),
                    Textarea::make('homepage_content.aesthetics_gallery.description')->label('Description')->rows(2)->columnSpanFull(),
                    Repeater::make('homepage_content.aesthetics_gallery.cards')
                        ->label('Gallery cards')
                        ->schema([
                            TextInput::make('badge')->label('Badge')->maxLength(80),
                            TextInput::make('title')->label('Title')->required()->maxLength(120),
                            TextInput::make('description')->label('Subtitle')->maxLength(200)->columnSpanFull(),
                            TextInput::make('collection_slug')
                                ->label('Collection slug')
                                ->helperText('e.g. skincare — links to /collections?cat=…')
                                ->maxLength(80),
                            AdminImageUpload::make('image_url', AdminImageUpload::PRESET_HOME_GALLERY)
                                ->label('Card image')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])->columns(2),
            Section::make('Featured promo banner')
                ->description('Wide purple promo with optional hero product and image.')
                ->schema([
                    TextInput::make('homepage_content.featured_promo.eyebrow')->label('Eyebrow')->maxLength(80),
                    TextInput::make('homepage_content.featured_promo.title')->label('Title (no product)')->maxLength(160)->columnSpanFull(),
                    Textarea::make('homepage_content.featured_promo.description')->label('Description')->rows(3)->columnSpanFull(),
                    Select::make('homepage_content.featured_promo.product_id')
                        ->label('Featured product (optional)')
                        ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->native(false),
                    TextInput::make('homepage_content.featured_promo.visual_title')->label('Visual card title')->maxLength(120),
                    TextInput::make('homepage_content.featured_promo.visual_subtitle')->label('Visual card subtitle')->maxLength(120),
                    TextInput::make('homepage_content.featured_promo.visual_badge')->label('Visual badge')->maxLength(60),
                    AdminImageUpload::make('homepage_content.featured_promo.image_url', AdminImageUpload::PRESET_HOME_GALLERY)
                        ->label('Right-side image')
                        ->columnSpanFull(),
                ])->columns(2),
            Section::make('Trending catalog header')
                ->schema([
                    TextInput::make('homepage_content.trending.badge')->label('Badge')->maxLength(120),
                    TextInput::make('homepage_content.trending.heading')->label('Heading')->maxLength(200)->columnSpanFull(),
                    Textarea::make('homepage_content.trending.description')->label('Description')->rows(2)->columnSpanFull(),
                ])->columns(2),
            Section::make('Celebrity endorsements')
                ->description('Cards with image, optional video URL, or product link.')
                ->schema([
                    TextInput::make('homepage_content.endorsements.badge')->label('Badge')->maxLength(120),
                    TextInput::make('homepage_content.endorsements.heading')->label('Heading')->maxLength(200)->columnSpanFull(),
                    Textarea::make('homepage_content.endorsements.description')->label('Description')->rows(2)->columnSpanFull(),
                    Repeater::make('homepage_content.endorsements.cards')
                        ->label('Endorsement cards')
                        ->schema([
                            TextInput::make('name')->label('Name')->required()->maxLength(120),
                            TextInput::make('badge')->label('Topic badge')->maxLength(60),
                            Textarea::make('quote')->label('Quote')->rows(2)->columnSpanFull(),
                            TextInput::make('video_url')
                                ->label('Video URL')
                                ->helperText('YouTube or external link — overrides product link')
                                ->url()
                                ->maxLength(500)
                                ->columnSpanFull(),
                            Select::make('product_id')
                                ->label('Link to product')
                                ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->native(false),
                            AdminImageUpload::make('image_url', AdminImageUpload::PRESET_HOME_GALLERY)
                                ->label('Portrait image')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ];
    }
}
