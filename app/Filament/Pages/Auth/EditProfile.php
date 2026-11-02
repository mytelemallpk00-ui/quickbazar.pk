<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Forms\AdminImageUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form;
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('Profile & contact')
                            ->description('Your details appear in the admin header and on account records.')
                            ->schema([
                                AdminImageUpload::make('profile_photo', AdminImageUpload::PRESET_ADMIN_AVATAR)
                                    ->label('Profile photo')
                                    ->imageEditor()
                                    ->columnSpanFull(),
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                TextInput::make('phone')
                                    ->label('Contact number')
                                    ->tel()
                                    ->maxLength(32)
                                    ->placeholder('+92 300 0000000'),
                                TextInput::make('cnic_no')
                                    ->label('CNIC number')
                                    ->maxLength(20)
                                    ->placeholder('12345-1234567-1')
                                    ->helperText('Format: 12345-1234567-1'),
                                TextInput::make('job_title')
                                    ->label('Job title')
                                    ->maxLength(120)
                                    ->placeholder('Store manager'),
                                Textarea::make('address')
                                    ->label('Address')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Section::make('Password')
                            ->description('Leave blank to keep your current password.')
                            ->schema([
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ])
                            ->columns(2),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }
}
