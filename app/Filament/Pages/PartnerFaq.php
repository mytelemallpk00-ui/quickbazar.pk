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

class PartnerFaq extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;
    use ManagesStorefrontCmsPage;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CONTENT;
    }

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'FAQ';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'FAQ';

    protected static ?string $slug = 'partner-faq';

    protected static string $view = 'filament.pages.cms-page-editor';

    protected static function cmsPageSlug(): string
    {
        return 'faq';
    }

    protected static function cmsPageType(): string
    {
        return CmsPage::TYPE_FAQ;
    }

    protected static function cmsDefaultTitle(): string
    {
        return 'FAQ';
    }

    public function form(Form $form): Form
    {
        return $this->cmsPageForm($form);
    }
}
