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

class PartnerEvents extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;
    use ManagesStorefrontCmsPage;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CONTENT;
    }

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Events';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Events';

    protected static ?string $slug = 'partner-events';

    protected static string $view = 'filament.pages.cms-page-editor';

    protected static function cmsPageSlug(): string
    {
        return 'events';
    }

    protected static function cmsPageType(): string
    {
        return CmsPage::TYPE_EVENTS;
    }

    protected static function cmsDefaultTitle(): string
    {
        return 'Events';
    }

    public function form(Form $form): Form
    {
        return $this->cmsPageForm($form);
    }
}
