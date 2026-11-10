<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Support\AdminPermissions;
use App\Support\AiSettings;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class AiIntegrationSettings extends Page implements HasForms
{
    use AuthorizesAdminPage;

    public static function canAccess(): bool
    {
        return false;
    }
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'AI integration';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 93;

    protected static ?string $title = 'AI integration';

    protected static ?string $slug = 'ai-integration';

    protected static string $view = 'filament.pages.ai-integration-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(AiSettings::forAdminForm());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('ai_help')
                    ->label('')
                    ->content(new HtmlString(
                        '<p class="text-sm text-gray-600 dark:text-gray-400">'
                        .'Keys are stored securely in your site database (no SSH needed). '
                        .'<strong>Smart SEO fill</strong> works without any key. '
                        .'Gemini Flash &amp; Pro need a free key from '
                        .'<a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener" class="text-primary-600 font-semibold underline">Google AI Studio</a> '
                        .'(this is <em>not</em> the same as signing into Gmail in the browser — Google does not allow that login to power your store backend).'
                        .'</p>'
                    ))
                    ->columnSpanFull(),
                Section::make('API keys')
                    ->schema([
                        TextInput::make('ai_gemini_api_key')
                            ->label('Gemini key on file')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Not set yet'),
                        TextInput::make('ai_gemini_api_key_input')
                            ->label('Add or replace Gemini API key')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->helperText('Free tier works with Gemini Flash. Pro model uses the same key (quota/billing may apply).'),
                        TextInput::make('ai_openai_api_key')
                            ->label('OpenAI key on file')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Not set yet'),
                        TextInput::make('ai_openai_api_key_input')
                            ->label('Add or replace OpenAI API key')
                            ->password()
                            ->revealable()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        AiSettings::saveFromAdmin($this->form->getState());

        $this->form->fill(AiSettings::forAdminForm());

        Notification::make()
            ->title('AI keys saved')
            ->body('Product SEO buttons can now use Gemini and/or OpenAI.')
            ->success()
            ->send();
    }
}
