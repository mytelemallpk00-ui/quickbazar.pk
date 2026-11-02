<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Filament\Forms\HomepageContentForm;
use App\Support\AdminPermissions;
use App\Support\HomepageContent;
use App\Support\SiteSettings;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class HomepageSettings extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Homepage banners';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 95;

    protected static ?string $title = 'Homepage banners & images';

    protected static ?string $slug = 'homepage-settings';

    protected static string $view = 'filament.pages.homepage-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'homepage_content' => SiteSettings::homepage(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(HomepageContentForm::schema())
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $previous = SiteSettings::get('homepage_content', []);
        if (! is_array($previous)) {
            $previous = [];
        }

        $content = $state['homepage_content'] ?? [];
        if (! is_array($content)) {
            $content = [];
        }

        $content = HomepageContent::preserveImageFields($content, $previous);
        $content = HomepageContent::mergeWithDefaults($content);

        SiteSettings::setHomepage($content);

        Notification::make()
            ->title('Homepage saved')
            ->body('Hero, promo cards, and category images are live on the storefront.')
            ->success()
            ->send();
    }
}
