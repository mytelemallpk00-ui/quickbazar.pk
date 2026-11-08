<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $layout = 'filament.components.layout.beautage-auth';

    protected static string $view = 'filament.pages.auth.login';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isDiscovered = false;

    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function getSubHeading(): string | Htmlable | null
    {
        return null;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Admin sign in — Shop Pakistan';
    }

    public function getMaxWidth(): MaxWidth | string | null
    {
        return MaxWidth::FiveExtraLarge;
    }

    public function fillDemoCredentials(): void
    {
        $this->form->fill([
            'email' => 'admin@beautage.com.pk',
            'password' => '',
            'remember' => true,
        ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->placeholder('name@email.com')
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getAuthenticateFormAction(): \Filament\Actions\Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Sign in');
    }

    public function form(Form $form): Form
    {
        return $form;
    }
}
