<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Models\Category;
use App\Models\ScheduledPost;
use App\Services\Social\SocialScheduleBuilder;
use App\Support\AdminPermissions;
use App\Support\Social\SocialCredentialResolver;
use App\Support\Social\SocialPlatforms;
use Carbon\Carbon;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action as NotifyAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SocialPostScheduler extends Page implements HasForms, HasTable
{
    use AuthorizesAdminPage;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Post scheduler';

    protected static ?string $navigationGroup = 'Social Automation';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Create & schedule posts';

    protected static ?string $slug = 'social-post-scheduler';

    protected static string $view = 'filament.pages.social-post-scheduler';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'category' => Category::query()->where('is_active', true)->orderBy('name')->value('name') ?? 'Skincare',
            'post_limit' => '5',
            'platforms' => ['facebook', 'telegram'],
            'campaign_preset' => 'week',
            'posting_frequency' => '24',
            'interval_hours' => 24,
            'start_at' => now()->addHour()->startOfHour(),
            'end_at' => now()->addDays(7)->endOfDay(),
            'media_path' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Step 1 — Kya promote karna hai?')
                    ->description('Store category choose karein. AI us theme par captions likhega.')
                    ->schema([
                        Select::make('category')
                            ->label('Product category / topic')
                            ->options(fn () => Category::query()->where('is_active', true)->orderBy('name')->pluck('name', 'name'))
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),
                        Select::make('post_limit')
                            ->label('Kitni posts banani hain?')
                            ->options([
                                '3' => '3 posts — quick test',
                                '5' => '5 posts — recommended',
                                '10' => '10 posts — weekly campaign',
                                '15' => '15 posts — full campaign',
                            ])
                            ->required()
                            ->native(false),
                        FileUpload::make('media_path')
                            ->label('Photo / video (optional)')
                            ->disk('public')
                            ->directory('social-queue')
                            ->acceptedFileTypes(['image/*', 'video/mp4', 'video/quicktime'])
                            ->maxSize(20480)
                            ->helperText('Instagram ke liye image zaroori. YouTube ke liye video file choose karein.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Step 2 — Kahan post karna hai?')
                    ->description('Jitne platforms select karein, utni jagah same caption jayega.')
                    ->schema([
                        CheckboxList::make('platforms')
                            ->label('Auto-publish channels')
                            ->options(SocialPlatforms::schedulableOptions())
                            ->descriptions(function (): array {
                                $resolver = app(SocialCredentialResolver::class);
                                $descriptions = [];
                                foreach (SocialPlatforms::schedulable() as $platform) {
                                    $descriptions[$platform] = $resolver->isConfigured($platform)
                                        ? '✓ Connected — publish ho sakta hai'
                                        : '⚠ Pehle Channel connections par token lagayein';
                                }

                                return $descriptions;
                            })
                            ->helperText('Google Business Profile, Pinterest, Shopping, etc. credentials ke liye Channel connections use karein — auto-post jaldi add hoga.')
                            ->columns(2)
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Step 3 — Kab post hon?')
                    ->description('Duration aur frequency set karein — dates automatic adjust ho jati hain.')
                    ->schema([
                        Select::make('campaign_preset')
                            ->label('Campaign duration')
                            ->options([
                                'week' => 'Next 7 days',
                                'two_weeks' => 'Next 14 days',
                                'month' => 'Next 30 days',
                                'custom' => 'Custom dates (advanced)',
                            ])
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                                $this->applyCampaignPreset($state ?? 'week', $set, $get);
                            })
                            ->required()
                            ->native(false),
                        Select::make('posting_frequency')
                            ->label('Posting frequency')
                            ->options([
                                '12' => 'Twice a day',
                                '24' => 'Once a day (recommended)',
                                '48' => 'Every 2 days',
                                '72' => 'Every 3 days',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (?string $state, Set $set) => $set('interval_hours', (int) ($state ?: 24)))
                            ->required()
                            ->native(false),
                        DateTimePicker::make('start_at')
                            ->label('First post time')
                            ->required()
                            ->seconds(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                $preset = $get('campaign_preset');
                                if ($preset && $preset !== 'custom') {
                                    $this->applyCampaignPreset($preset, $set, $get);
                                }
                            }),
                        DateTimePicker::make('end_at')
                            ->label('Last day of campaign')
                            ->required()
                            ->seconds(false)
                            ->hidden(fn (Get $get): bool => ($get('campaign_preset') ?? 'week') !== 'custom'),
                        Select::make('interval_hours')
                            ->label('Hours between posts')
                            ->options([
                                12 => '12 hours',
                                24 => '24 hours',
                                48 => '48 hours',
                                72 => '72 hours',
                            ])
                            ->hidden(fn (Get $get): bool => ($get('campaign_preset') ?? 'week') !== 'custom')
                            ->dehydrated(true),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function applyCampaignPreset(string $preset, Set $set, Get $get): void
    {
        if ($preset === 'custom') {
            return;
        }

        $start = Carbon::parse($get('start_at') ?? now()->addHour());
        $days = match ($preset) {
            'two_weeks' => 14,
            'month' => 30,
            default => 7,
        };

        $set('end_at', $start->copy()->addDays($days)->endOfDay());
    }

    /** @return array{pending: int, published: int, failed: int, next_label: string} */
    public function getQueueStats(): array
    {
        $next = ScheduledPost::query()
            ->where('status', ScheduledPost::STATUS_PENDING)
            ->orderBy('scheduled_at')
            ->value('scheduled_at');

        return [
            'pending' => ScheduledPost::query()->where('status', ScheduledPost::STATUS_PENDING)->count(),
            'published' => ScheduledPost::query()->where('status', ScheduledPost::STATUS_PUBLISHED)->count(),
            'failed' => ScheduledPost::query()->where('status', ScheduledPost::STATUS_FAILED)->count(),
            'next_label' => $next
                ? Carbon::parse($next)->timezone(config('app.timezone'))->format('M j, g:i A')
                : '—',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ScheduledPost::query()->latest('scheduled_at'))
            ->emptyStateHeading('No posts scheduled yet')
            ->emptyStateDescription('Step 1–3 fill karke “Create my post schedule” dabayein.')
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Goes live')
                    ->dateTime('M j, g:i A')
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Topic')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('caption')
                    ->label('Caption preview')
                    ->limit(50)
                    ->tooltip(fn (ScheduledPost $record): string => (string) $record->caption),
                TextColumn::make('platforms')
                    ->label('Where')
                    ->formatStateUsing(function ($state): string {
                        if (! is_array($state)) {
                            return '';
                        }

                        return collect($state)
                            ->map(fn ($p) => SocialPlatforms::label((string) $p))
                            ->implode(', ');
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        ScheduledPost::STATUS_PUBLISHED => 'Published',
                        ScheduledPost::STATUS_FAILED => 'Failed',
                        default => 'Waiting',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ScheduledPost::STATUS_PUBLISHED => 'success',
                        ScheduledPost::STATUS_FAILED => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        ScheduledPost::STATUS_PENDING => 'Waiting',
                        ScheduledPost::STATUS_PUBLISHED => 'Published',
                        ScheduledPost::STATUS_FAILED => 'Failed',
                    ]),
            ])
            ->actions([
                Action::make('view')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Post preview')
                    ->modalContent(fn (ScheduledPost $record) => view('filament.partials.social-post-preview', ['post' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Action::make('retry')
                    ->label('Try again')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (ScheduledPost $record) => $record->status === ScheduledPost::STATUS_FAILED)
                    ->action(function (ScheduledPost $record): void {
                        $record->forceFill([
                            'status' => ScheduledPost::STATUS_PENDING,
                            'error_log' => null,
                        ])->save();

                        \App\Jobs\PublishScheduledPostJob::dispatch($record);

                        Notification::make()
                            ->title('Post dubara queue mein hai')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    /** @return array{ready: list<string>, missing: list<string>, ready_count: int, total: int} */
    public function getConnectionSummary(): array
    {
        return app(SocialCredentialResolver::class)->connectionSummary();
    }

    public function generateSchedule(SocialScheduleBuilder $builder): void
    {
        $state = $this->form->getState();

        $resolver = app(SocialCredentialResolver::class);
        $notReady = collect($state['platforms'] ?? [])
            ->filter(fn (string $platform): bool => ! $resolver->isConfigured($platform));

        if ($notReady->isNotEmpty()) {
            $names = $notReady->map(fn (string $p) => SocialPlatforms::label($p))->implode(', ');
            Notification::make()
                ->title('Pehle accounts connect karein')
                ->body("In platforms par abhi token nahi: {$names}. Connect accounts page par save karein, phir dubara try karein.")
                ->warning()
                ->actions([
                    NotifyAction::make('connect')
                        ->label('Connect accounts')
                        ->url(SocialApiCredentials::getUrl()),
                ])
                ->send();

            return;
        }

        $media = $state['media_path'] ?? null;
        if (is_array($media)) {
            $media = $media[array_key_first($media)] ?? null;
        }

        $interval = (int) ($state['interval_hours'] ?? $state['posting_frequency'] ?? 24);

        try {
            $count = $builder->createQueue([
                'category' => (string) $state['category'],
                'post_limit' => (int) ($state['post_limit'] ?? 5),
                'platforms' => array_values($state['platforms'] ?? []),
                'start_at' => (string) $state['start_at'],
                'end_at' => (string) $state['end_at'],
                'interval_hours' => max(1, $interval),
                'media_path' => filled($media) ? (string) $media : null,
            ]);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Schedule nahi bani')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title("{$count} posts schedule ho gayi")
            ->body('Neeche list mein “Waiting” dikhegi — time par auto publish hogi.')
            ->success()
            ->send();

        $this->resetTable();
    }

    public function getSubheading(): ?string
    {
        return '3 simple steps — AI captions, automatic posting';
    }
}
