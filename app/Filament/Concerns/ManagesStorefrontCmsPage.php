<?php

namespace App\Filament\Concerns;

use App\Filament\Forms\SeoSection;
use App\Models\CmsPage;
use App\Services\Newsletter\NewsletterBroadcastService;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

trait ManagesStorefrontCmsPage
{
    /** @var array<string, mixed>|null */
    public ?array $data = [];

    abstract protected static function cmsPageSlug(): string;

    abstract protected static function cmsPageType(): string;

    abstract protected static function cmsDefaultTitle(): string;

    public function mount(): void
    {
        $this->form->fill($this->resolveCmsPageRecord()->attributesToArray());
    }

    public function cmsPageForm(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('CmsPage')
                    ->tabs([
                        Tabs\Tab::make('Hero')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('title')->label('Page title')->required(),
                                TextInput::make('hero_badge'),
                                TextInput::make('hero_title')->columnSpanFull(),
                                Textarea::make('hero_subtitle')->rows(3)->columnSpanFull(),
                                Toggle::make('is_published')->label('Published on storefront')->default(true),
                                Toggle::make('notify_partner_subscribers')
                                    ->label('Email Partner / IR list after save')
                                    ->helperText('Sends a themed briefing to all Partner / IR newsletter subscribers using this page\'s hero content.')
                                    ->default(false)
                                    ->columnSpanFull(),
                            ])->columns(2),
                        Tabs\Tab::make('Body')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                RichEditor::make('content_html')->columnSpanFull(),
                            ]),
                        SeoSection::tab('title', 'slug', 'hero_subtitle'),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $state['slug'] = static::cmsPageSlug();
        $state['page_type'] = static::cmsPageType();

        $notifyPartners = (bool) ($state['notify_partner_subscribers'] ?? false);
        unset($state['notify_partner_subscribers']);

        $page = $this->resolveCmsPageRecord();
        $page->fill($state);
        $page->save();

        $body = 'Changes are live on the storefront.';
        if ($notifyPartners) {
            $broadcast = app(NewsletterBroadcastService::class)->sendPartnerAutoFromCmsPage($page->fresh());
            if ($broadcast) {
                $body .= ' Partner / IR newsletter queued to '.$broadcast->recipients_count.' subscriber(s).';
            } else {
                $body .= ' Partner email not queued (no subscribers or not signed in).';
            }
        }

        Notification::make()
            ->title(static::cmsDefaultTitle().' saved')
            ->body($body)
            ->success()
            ->send();
    }

    public function getLivePreviewUrl(): string
    {
        $slug = static::cmsPageSlug();
        if (\Illuminate\Support\Facades\Route::has($slug)) {
            return route($slug);
        }

        return url('/'.$slug);
    }

    protected function resolveCmsPageRecord(): CmsPage
    {
        $slug = static::cmsPageSlug();

        $existing = CmsPage::query()->where('slug', $slug)->first();
        if ($existing) {
            return $existing;
        }

        $defaults = CmsPage::defaultPage($slug);
        if ($defaults) {
            $defaults->save();

            return $defaults->fresh();
        }

        $page = new CmsPage([
            'slug' => $slug,
            'title' => static::cmsDefaultTitle(),
            'page_type' => static::cmsPageType(),
            'hero_title' => static::cmsDefaultTitle(),
            'is_published' => true,
        ]);
        $page->save();

        return $page->fresh();
    }
}
