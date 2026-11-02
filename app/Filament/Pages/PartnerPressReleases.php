<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Filament\Concerns\ManagesStorefrontCmsPage;
use App\Models\CmsPage;
use App\Support\AdminPermissions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class PartnerPressReleases extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;
    use ManagesStorefrontCmsPage;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CONTENT;
    }

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Press Releases';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Press Releases';

    protected static ?string $slug = 'partner-press-releases';

    protected static string $view = 'filament.pages.cms-page-editor';

    protected static function cmsPageSlug(): string
    {
        return 'press-releases';
    }

    protected static function cmsPageType(): string
    {
        return CmsPage::TYPE_PRESS_RELEASES;
    }

    protected static function cmsDefaultTitle(): string
    {
        return 'Press Releases';
    }

    public function form(Form $form): Form
    {
        return $this->cmsPageForm($form);
    }
}
