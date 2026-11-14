<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class FooterSettingsForm
{
    /** @return array<int, \Filament\Forms\Components\Component> */
    public static function schema(): array
    {
        return [
            Section::make('Brand column')
                ->description('Round footer logo, about text, and social profiles.')
                ->schema([
                    AdminImageUpload::make('footer_logo_url', AdminImageUpload::PRESET_SITE_LOGO)
                        ->label('Footer logo')
                        ->helperText('Wide logo, 1400 × 430 px, transparent PNG. Max 2 MB. Leave empty to use the Quick Bazar logo.')
                        ->columnSpanFull(),
                    Textarea::make('footer_settings.about_text')
                        ->label('About text')
                        ->rows(3)
                        ->columnSpanFull(),
                    Toggle::make('footer_settings.show_tagline_under_logo')
                        ->label('Show site tagline under logo')
                        ->default(true),
                    Repeater::make('footer_settings.social_links')
                        ->label('Social links')
                        ->schema(self::socialLinkSchema())
                        ->collapsible()
                        ->columnSpanFull(),
                ])->columns(2),
            Section::make('Trust badges (top strip)')
                ->schema([
                    Toggle::make('footer_settings.trust_enabled')
                        ->label('Show trust badge strip')
                        ->default(true),
                    Repeater::make('footer_settings.trust_badges')
                        ->label('Badges')
                        ->schema(self::trustBadgeSchema())
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
            Section::make('Newsletter')
                ->description('Customer store newsletter (promos & new arrivals).')
                ->schema([
                    Toggle::make('footer_settings.newsletter_enabled')
                        ->label('Show newsletter block')
                        ->default(true),
                    TextInput::make('footer_settings.newsletter_badge')->label('Badge label'),
                    TextInput::make('footer_settings.newsletter_heading')->label('Heading'),
                    Textarea::make('footer_settings.newsletter_description')->label('Description')->rows(2)->columnSpanFull(),
                    TextInput::make('footer_settings.newsletter_placeholder')->label('Email placeholder'),
                    TextInput::make('footer_settings.newsletter_button')->label('Button label'),
                ])->columns(2),
            Section::make('Quick Bazar PVT Ltd. Partner hub')
                ->description('Highlighted footer block: partner pill buttons and a separate briefing-list newsletter.')
                ->schema([
                    Toggle::make('footer_settings.partner_hub_enabled')
                        ->label('Show partner hub section')
                        ->default(true),
                    TextInput::make('footer_settings.partner_hub_badge')->label('Badge'),
                    TextInput::make('footer_settings.partner_hub_title')
                        ->label('Section title')
                        ->default('Quick Bazar PVT Ltd. Partner'),
                    Textarea::make('footer_settings.partner_hub_subtitle')->label('Subtitle')->rows(2)->columnSpanFull(),
                    Repeater::make('footer_settings.partner_hub_buttons')
                        ->label('Partner buttons')
                        ->schema(self::partnerButtonSchema())
                        ->collapsible()
                        ->columnSpanFull(),
                    Toggle::make('footer_settings.partner_newsletter_enabled')
                        ->label('Show partner newsletter')
                        ->default(true),
                    TextInput::make('footer_settings.partner_newsletter_badge')->label('Newsletter badge'),
                    TextInput::make('footer_settings.partner_newsletter_heading')->label('Newsletter heading'),
                    Textarea::make('footer_settings.partner_newsletter_description')->label('Newsletter description')->rows(2)->columnSpanFull(),
                    TextInput::make('footer_settings.partner_newsletter_placeholder')->label('Email placeholder'),
                    TextInput::make('footer_settings.partner_newsletter_button')->label('Subscribe button'),
                ])->columns(2),
            Section::make('Policy links (horizontal strip)')
                ->schema([
                    Repeater::make('footer_settings.policy_links')
                        ->label('Links')
                        ->schema(self::footerLinkSchema())
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
            Section::make('Link columns')
                ->description('Categories, Customer Care, About — add or reorder columns and links.')
                ->schema([
                    Repeater::make('footer_settings.link_columns')
                        ->label('Columns')
                        ->schema([
                            TextInput::make('title')->label('Column title')->required(),
                            Repeater::make('links')
                                ->label('Links')
                                ->schema(self::footerLinkSchema())
                                ->collapsible(),
                        ])
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
            Section::make('Mobile app')
                ->schema([
                    Toggle::make('footer_settings.mobile_app_enabled')
                        ->label('Show mobile app column')
                        ->default(true),
                    TextInput::make('footer_settings.mobile_app_title')->label('Column title'),
                    Textarea::make('footer_settings.mobile_app_description')->label('Description')->rows(2)->columnSpanFull(),
                    TextInput::make('footer_settings.mobile_app_ios_href')
                        ->label('App Store URL')
                        ->url()
                        ->placeholder('https://'),
                    TextInput::make('footer_settings.mobile_app_android_href')
                        ->label('Google Play URL')
                        ->url()
                        ->placeholder('https://'),
                ])->columns(2),
            Section::make('Copyright')
                ->schema([
                    TextInput::make('footer_settings.copyright_line')
                        ->label('Copyright line')
                        ->columnSpanFull(),
                ]),
        ];
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    private static function trustBadgeSchema(): array
    {
        return [
            Toggle::make('enabled')->label('Visible')->default(true),
            TextInput::make('icon')->label('Lucide icon')->placeholder('truck')->required(),
            TextInput::make('title')->label('Title')->required(),
            TextInput::make('subtitle')->label('Subtitle'),
        ];
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    private static function footerLinkSchema(): array
    {
        return [
            Toggle::make('enabled')->label('Visible')->default(true),
            TextInput::make('label')->label('Label')->required(),
            TextInput::make('href')
                ->label('Link')
                ->required()
                ->helperText('Route name (e.g. help, collections), path (/blog), or full https URL.'),
            TextInput::make('icon')->label('Lucide icon')->placeholder('help-circle'),
        ];
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    private static function partnerButtonSchema(): array
    {
        return [
            Toggle::make('enabled')->label('Visible')->default(true),
            TextInput::make('label')->label('Button label')->required(),
            TextInput::make('href')
                ->label('Link')
                ->required()
                ->helperText('Route name or slug, e.g. faq, press-releases, events'),
            Select::make('style')
                ->label('Button style')
                ->options([
                    'outline' => 'Cyan outline',
                    'outline-white' => 'White outline',
                    'apply' => 'Cyan outline (accent)',
                    'portal' => 'Purple fill (accent)',
                ])
                ->default('outline')
                ->required(),
            TextInput::make('icon')->label('Lucide icon')->placeholder('newspaper'),
        ];
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    private static function socialLinkSchema(): array
    {
        return [
            Toggle::make('enabled')->label('Visible')->default(true),
            TextInput::make('platform')->label('Platform name')->placeholder('Instagram'),
            TextInput::make('icon')->label('Lucide icon')->placeholder('instagram')->required(),
            TextInput::make('href')->label('Profile URL')->url()->required(),
        ];
    }
}
