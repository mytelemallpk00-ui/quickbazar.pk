<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Support\AdminPermissions;
use App\Support\SiteSettings;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReviewSettings extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::REVIEWS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Review settings';

    protected static ?string $navigationGroup = 'Customer Reviews';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Review settings';

    protected static ?string $slug = 'review-settings';

    protected static string $view = 'filament.pages.review-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'approval_mode' => SiteSettings::reviewsAutoApprove() ? 'auto' : 'manual',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Storefront review approval')
                    ->description('Controls what happens when a customer submits a review from the product page.')
                    ->schema([
                        Radio::make('approval_mode')
                            ->label('Approval mode')
                            ->options([
                                'manual' => 'Manual approval — reviews stay hidden until you approve them in All reviews',
                                'auto' => 'Auto approval — new customer reviews go live immediately (photos included)',
                            ])
                            ->required()
                            ->inline(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $auto = ($state['approval_mode'] ?? 'manual') === 'auto';

        SiteSettings::set('reviews_auto_approve', $auto, 'reviews');

        Notification::make()
            ->title('Review settings saved')
            ->success()
            ->send();
    }
}
