<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Resources\SocialMarketingChannelResource\Pages;
use App\Models\SocialMarketingChannel;
use App\Support\AdminPermissions;
use App\Support\Social\SocialChannelCatalog;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SocialMarketingChannelResource extends Resource
{
    use AuthorizesAdminAccess;

    public static function canViewAny(): bool
    {
        return false;
    }

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SOCIAL;
    }

    protected static ?string $model = SocialMarketingChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Social Automation';

    protected static ?string $navigationLabel = 'Marketing channels';

    protected static ?int $navigationSort = 0;

    protected static ?string $modelLabel = 'marketing channel';

    protected static ?string $pluralModelLabel = 'Marketing channels';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Channel identity')
                ->schema([
                    TextInput::make('label')
                        ->label('Display name (tab title)')
                        ->required()
                        ->maxLength(120)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                            if (filled($get('key'))) {
                                return;
                            }
                            $set('key', Str::slug($state ?? '', '_'));
                        }),
                    TextInput::make('key')
                        ->label('System key')
                        ->required()
                        ->maxLength(64)
                        ->regex('/^[a-z0-9_]+$/')
                        ->unique(ignoreRecord: true)
                        ->disabledOn('edit')
                        ->dehydrated()
                        ->helperText('Lowercase letters, numbers, underscores — used in API storage.'),
                    Textarea::make('guide')
                        ->label('Setup instructions')
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Shown on Connect accounts tab for this channel.'),
                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(9999),
                    Toggle::make('is_enabled')
                        ->label('Show in admin & connections')
                        ->default(true),
                    Toggle::make('supports_scheduling')
                        ->label('Allow in post scheduler')
                        ->helperText('Only channels with an auto-publish driver (Facebook, Instagram, etc.) can actually publish scheduled posts.')
                        ->default(false),
                ])
                ->columns(2),
            Section::make('Credential fields')
                ->description('Define which tokens/IDs admins paste under Channel connections.')
                ->schema([
                    Repeater::make('credential_fields')
                        ->label('Fields')
                        ->schema([
                            TextInput::make('key')->label('Field key')->required()->maxLength(64),
                            TextInput::make('label')->label('Field label')->required()->maxLength(120),
                            Toggle::make('secret')->label('Secret (password field)')->default(true),
                            Toggle::make('required')->label('Required for “Ready” status')->default(true),
                        ])
                        ->columns(4)
                        ->columnSpanFull()
                        ->defaultItems(1)
                        ->addActionLabel('Add credential field'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->description(fn (SocialMarketingChannel $record): string => $record->key),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('supports_scheduling')
                    ->label('Scheduler')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialMarketingChannels::route('/'),
            'create' => Pages\CreateSocialMarketingChannel::route('/create'),
            'edit' => Pages\EditSocialMarketingChannel::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderBy('sort_order')->orderBy('label');
    }

}
