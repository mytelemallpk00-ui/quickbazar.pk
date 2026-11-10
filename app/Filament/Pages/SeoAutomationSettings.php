<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Services\Seo\SitemapGenerator;
use App\Support\AdminPermissions;
use App\Support\SeoAutomationSettings as SeoAutomationSettingsStore;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class SeoAutomationSettings extends Page implements HasForms
{
    use AuthorizesAdminPage;

    public static function canAccess(): bool
    {
        return false;
    }
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CATALOG;
    }

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $navigationGroup = 'AI SEO';

    protected static ?string $navigationLabel = 'SEO automation';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'SEO automation & sitemaps';

    protected static ?string $slug = 'seo-automation-settings';

    protected static string $view = 'filament.pages.seo-automation-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SeoAutomationSettingsStore::all());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Placeholder::make('links')
                ->label('')
                ->content(new HtmlString(
                    '<p class="text-sm text-gray-600">Google Indexing API: '
                    .'<a class="text-primary-600 underline font-semibold" href="'.route('filament.admin.pages.social-google-indexing').'">Search Indexing</a>'
                    .' · Manual product push still available on each product edit screen.</p>'
                ))
                ->columnSpanFull(),
            Section::make('XML sitemap')
                ->schema([
                    Toggle::make('sitemap_enabled')->label('Generate sitemap_index.xml + parts'),
                    Toggle::make('sitemap_include_products')->label('Include product URLs'),
                    Toggle::make('sitemap_include_pseo')->label('Include pSEO landing pages'),
                    Toggle::make('sitemap_include_cms')->label('Include core content pages (IR, FAQ, etc.)'),
                ])->columns(2),
            Section::make('Faceted catalog SEO')
                ->description('Collections page canonical & robots when query parameters are used.')
                ->schema([
                    TagsInput::make('faceted_indexed_params')
                        ->label('Indexable query params')
                        ->placeholder('cat')
                        ->helperText('Only these URL parameters keep index,follow. Others trigger noindex when extra filters appear.'),
                    Toggle::make('faceted_noindex_when_extra_filters')->label('noindex when non-whitelisted params present'),
                ]),
            Section::make('Automation on product save')
                ->schema([
                    Toggle::make('auto_index_on_product_save')
                        ->label('Push to Google Indexing API after product updates')
                        ->helperText('Requires service account under Social Automation → Channel connections.'),
                    Toggle::make('auto_ai_seo_on_product_create')
                        ->label('Auto-draft SEO fields when a new product is created (background job)')
                        ->helperText('Uses Smart SEO (same as product form AI). Skips if meta title & description already set.'),
                ])->columns(1),
        ])->statePath('data');
    }

    public function save(): void
    {
        SeoAutomationSettingsStore::save($this->form->getState());

        Notification::make()->title('SEO automation saved')->success()->send();
    }

    public function regenerateSitemap(SitemapGenerator $generator): void
    {
        $generator->generate();
        Notification::make()
            ->title('Sitemap regenerated')
            ->body('sitemap_index.xml and chunked sitemap-*.xml updated (max 50,000 URLs per file).')
            ->success()
            ->send();
    }
}
