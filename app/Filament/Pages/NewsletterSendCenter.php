<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Models\CmsPage;
use App\Models\NewsletterBroadcast;
use App\Models\NewsletterSubscriber;
use App\Models\Product;
use App\Services\Newsletter\NewsletterBroadcastService;
use App\Support\AdminPermissions;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class NewsletterSendCenter extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CONTENT;
    }

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Newsletter send center';

    protected static ?int $navigationSort = 8;

    protected static ?string $title = 'Newsletter send center';

    protected static ?string $slug = 'newsletter-send-center';

    protected static string $view = 'filament.pages.newsletter-send-center';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'store' => [
                'subject' => 'New at Beautage — products, bundles & offers',
                'preheader' => 'Hand-picked products and bundle savings inside.',
                'headline' => 'This week at Beautage',
                'sale_text' => 'Limited-time offers on flagship skincare & bundles.',
                'intro_html' => '<p>Explore new arrivals, bundle deals, and exclusive newsletter-only highlights.</p>',
                'product_ids' => [],
                'include_active_bundle' => true,
                'cta_label' => 'Shop collections',
                'cta_url' => url('/collections'),
            ],
            'partner' => [
                'cms_slug' => 'press-releases',
                'subject' => 'Beautage partner update',
                'preheader' => 'Corporate briefing from Beautage PVT Ltd.',
                'headline' => 'Partner update',
                'body_html' => '<p>Summary of the latest partner update.</p>',
                'cta_label' => 'Read full update',
                'cta_url' => route('press-releases'),
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        $storeCount = NewsletterSubscriber::query()->where('list', NewsletterSubscriber::LIST_STORE)->count();
        $partnerCount = NewsletterSubscriber::query()->where('list', NewsletterSubscriber::LIST_PARTNER)->count();

        return $form
            ->schema([
                Tabs::make('SendCenter')
                    ->tabs([
                        Tabs\Tab::make('Store newsletter')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([
                                Placeholder::make('store_count')
                                    ->label('Recipients')
                                    ->content(new HtmlString('<span class="text-sm font-semibold text-emerald-700">'.$storeCount.' store subscriber(s)</span>')),
                                TextInput::make('store.subject')->label('Email subject')->required()->columnSpanFull(),
                                TextInput::make('store.preheader')->label('Inbox preview line')->columnSpanFull(),
                                TextInput::make('store.headline')->label('Email headline')->required(),
                                TextInput::make('store.sale_text')->label('Sales / promo banner')->columnSpanFull(),
                                RichEditor::make('store.intro_html')->label('Intro message')->columnSpanFull(),
                                Select::make('store.product_ids')
                                    ->label('Featured products')
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                                    ->helperText('Shown as product cards with prices in the store-themed email.')
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\Toggle::make('store.include_active_bundle')
                                    ->label('Include latest active bundle deal block')
                                    ->default(true),
                                TextInput::make('store.cta_label')->label('Button label'),
                                TextInput::make('store.cta_url')->label('Button URL')->url(),
                                Actions::make([
                                    FormAction::make('sendStore')
                                        ->label('Send to all store subscribers')
                                        ->icon('heroicon-o-paper-airplane')
                                        ->color('success')
                                        ->requiresConfirmation()
                                        ->modalHeading('Send store newsletter')
                                        ->modalDescription('This sends one email to every Store newsletter subscriber.')
                                        ->action(fn () => $this->sendStoreBulk()),
                                ])->columnSpanFull(),
                            ])->columns(2),
                        Tabs\Tab::make('Partner updates')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Placeholder::make('partner_count')
                                    ->label('Recipients')
                                    ->content(new HtmlString('<span class="text-sm font-semibold text-violet-700">'.$partnerCount.' partner subscriber(s)</span>')),
                                Select::make('partner.cms_slug')
                                    ->label('Load from partner page')
                                    ->options([
                                        'press-releases' => 'Press Releases',
                                        'faq' => 'FAQ',
                                        'events' => 'Events',
                                    ])
                                    ->live(),
                                Actions::make([
                                    FormAction::make('loadPartnerPage')
                                        ->label('Pull hero & copy from page')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->action(fn (Get $get) => $this->loadPartnerFromCms((string) $get('partner.cms_slug'))),
                                ])->columnSpanFull(),
                                TextInput::make('partner.subject')->label('Email subject')->required()->columnSpanFull(),
                                TextInput::make('partner.preheader')->label('Inbox preview line')->columnSpanFull(),
                                TextInput::make('partner.headline')->label('Email headline')->required(),
                                RichEditor::make('partner.body_html')->label('Briefing body')->columnSpanFull(),
                                TextInput::make('partner.cta_label')->label('Button label'),
                                TextInput::make('partner.cta_url')->label('Button URL')->url(),
                                Actions::make([
                                    FormAction::make('sendPartner')
                                        ->label('Send to all partner subscribers')
                                        ->icon('heroicon-o-paper-airplane')
                                        ->color('success')
                                        ->requiresConfirmation()
                                        ->modalHeading('Send partner briefing')
                                        ->modalDescription('This sends one themed email to every partner subscriber.')
                                        ->action(fn () => $this->sendPartnerBulk()),
                                ])->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
                Section::make('Recent broadcasts')
                    ->schema([
                        Placeholder::make('history')
                            ->label('')
                            ->content(fn () => $this->renderBroadcastHistory()),
                    ]),
            ])
            ->statePath('data');
    }

    public function sendStoreBulk(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $store = $this->form->getState()['store'] ?? [];
        $service = app(NewsletterBroadcastService::class);

        $payload = [
            'subject' => $store['subject'] ?? 'Beautage store update',
            'preheader' => $store['preheader'] ?? null,
            'headline' => $store['headline'] ?? 'Beautage',
            'body_html' => $store['intro_html'] ?? null,
            'intro_html' => $store['intro_html'] ?? null,
            'sale_text' => $store['sale_text'] ?? null,
            'product_ids' => array_values(array_map('intval', $store['product_ids'] ?? [])),
            'cta_label' => $store['cta_label'] ?? 'Shop now',
            'cta_url' => $store['cta_url'] ?? url('/collections'),
            'badge' => 'STORE · PRODUCTS & SALES',
        ];

        if (! empty($store['include_active_bundle'])) {
            $payload['bundle_highlight'] = $service->activeBundleHighlight();
        }

        $broadcast = $service->queueStoreBroadcast($user, $payload);

        Notification::make()
            ->title('Store newsletter queued')
            ->body('Sending to '.$broadcast->recipients_count.' subscriber(s). Theme: products, bundles & sales.')
            ->success()
            ->send();
    }

    public function sendPartnerBulk(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $partner = $this->form->getState()['partner'] ?? [];
        $slug = $partner['cms_slug'] ?? null;
        $page = $slug ? CmsPage::query()->where('slug', $slug)->first() : null;

        $payload = [
            'subject' => $partner['subject'] ?? 'Partner update',
            'preheader' => $partner['preheader'] ?? null,
            'headline' => $partner['headline'] ?? 'Partner update',
            'body_html' => $partner['body_html'] ?? null,
            'cta_label' => $partner['cta_label'] ?? 'Read update',
            'cta_url' => $partner['cta_url'] ?? url('/press-releases'),
            'cms_slug' => $slug,
            'cms_page_type' => $page?->page_type,
            'badge' => 'PARTNER · IR UPDATES',
        ];

        $broadcast = app(NewsletterBroadcastService::class)->queuePartnerBroadcast($user, $payload);

        Notification::make()
            ->title('Partner briefing queued')
            ->body('Sending to '.$broadcast->recipients_count.' subscriber(s).')
            ->success()
            ->send();
    }

    public function loadPartnerFromCms(string $slug): void
    {
        if (blank($slug)) {
            return;
        }

        $page = CmsPage::query()->where('slug', $slug)->first()
            ?? CmsPage::defaultPage($slug);

        if (! $page) {
            Notification::make()->title('Page not found')->danger()->send();

            return;
        }

        $payload = app(NewsletterBroadcastService::class)->buildPartnerPayloadFromCmsPage($page);
        if (filled($page->content_html)) {
            $payload['body_html'] = $page->content_html;
        }

        $state = $this->form->getState();
        $state['partner'] = array_merge($state['partner'] ?? [], [
            'cms_slug' => $slug,
            'subject' => $payload['subject'],
            'preheader' => $payload['preheader'],
            'headline' => $payload['headline'],
            'body_html' => $payload['body_html'],
            'cta_label' => $payload['cta_label'],
            'cta_url' => $payload['cta_url'],
        ]);
        $this->form->fill($state);

        Notification::make()
            ->title('Loaded from '.$page->title)
            ->success()
            ->send();
    }

    protected function renderBroadcastHistory(): HtmlString
    {
        $rows = NewsletterBroadcast::query()->latest('id')->limit(8)->get();
        if ($rows->isEmpty()) {
            return new HtmlString('<p class="text-sm text-gray-500">No broadcasts sent yet.</p>');
        }

        $html = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-left text-gray-500"><th class="py-2">When</th><th>List</th><th>Subject</th><th>Status</th><th>Sent</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            $list = NewsletterSubscriber::listLabels()[$row->list] ?? $row->list;
            $html .= '<tr class="border-t border-gray-100"><td class="py-2 pr-3">'.e($row->created_at?->format('M j, Y H:i')).'</td>';
            $html .= '<td class="py-2 pr-3">'.e($list).'</td>';
            $html .= '<td class="py-2 pr-3">'.e($row->subject).'</td>';
            $html .= '<td class="py-2 pr-3">'.e($row->status).'</td>';
            $html .= '<td class="py-2">'.e($row->sent_count).' / '.e($row->recipients_count).'</td></tr>';
        }
        $html .= '</tbody></table></div>';

        return new HtmlString($html);
    }
}
