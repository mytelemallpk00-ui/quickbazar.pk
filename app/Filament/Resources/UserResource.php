<?php

namespace App\Filament\Resources;

use App\Filament\Forms\AdminImageUpload;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Support\AdminPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Admin users';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'admin user';

    protected static ?string $slug = 'users';

    public static function canViewAny(): bool
    {
        return auth()->user()?->canManageAdminUsers() ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny() && $record instanceof User && $record->id !== auth()->id();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile & contact')
                    ->schema([
                        AdminImageUpload::make('profile_photo', AdminImageUpload::PRESET_ADMIN_AVATAR)
                            ->label('Profile photo')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Full name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('Contact number')
                            ->tel()
                            ->maxLength(32),
                        Forms\Components\TextInput::make('cnic_no')
                            ->label('CNIC number')
                            ->maxLength(20)
                            ->placeholder('12345-1234567-1'),
                        Forms\Components\TextInput::make('job_title')
                            ->maxLength(120),
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Admin access')
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Can access admin panel')
                            ->default(true)
                            ->required()
                            ->disabled(fn (?User $record): bool => $record?->id === auth()->id()),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Role & permissions')
                    ->description('One super admin manages users and has every section. Other admins only see checked areas below (dashboard access is always included).')
                    ->schema([
                        Forms\Components\Select::make('admin_role')
                            ->label('Role')
                            ->options(function (?User $record): array {
                                $options = [
                                    'admin' => 'Admin (custom section access)',
                                ];

                                $canAssignSuper = ! User::superAdminExists($record?->id);

                                if ($canAssignSuper || $record?->isSuperAdmin()) {
                                    $options = [
                                        'super' => 'Super admin (full access + user management)',
                                        ...$options,
                                    ];
                                }

                                return $options;
                            })
                            ->default('admin')
                            ->required()
                            ->live()
                            ->native(false)
                            ->dehydrated(false)
                            ->disabled(fn (?User $record): bool => $record?->isSuperAdmin() && ! User::superAdminExists($record->id)),
                        Forms\Components\CheckboxList::make('admin_permissions')
                            ->label('Allowed admin sections')
                            ->options(AdminPermissions::labels())
                            ->columns(2)
                            ->bulkToggleable()
                            ->hidden(fn (Get $get): bool => $get('admin_role') === 'super')
                            ->required(fn (Get $get): bool => $get('admin_role') === 'admin')
                            ->helperText('Select every area this user may open in the admin panel.'),
                    ])
                    ->visible(fn (): bool => (bool) auth()->user()?->canManageAdminUsers()),
                Forms\Components\Section::make('Password')
                    ->description('Required when creating a user. Leave blank on edit to keep the current password.')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->dehydrated(false)
                            ->required(fn (Forms\Get $get, string $operation): bool => $operation === 'create' || filled($get('password'))),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('is_admin', true))
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('Photo')
                    ->circular()
                    ->getStateUsing(fn (User $record): ?string => $record->profilePhotoUrl())
                    ->checkFileExistence(false)
                    ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=5E1F70&background=F3E8FF'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->label('Contact')->toggleable(),
                Tables\Columns\TextColumn::make('cnic_no')->label('CNIC')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('job_title')->toggleable(),
                Tables\Columns\TextColumn::make('is_super_admin')
                    ->label('Role')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Super admin' : 'Admin')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('admin_permissions')
                    ->label('Sections')
                    ->formatStateUsing(function ($state, User $record): string {
                        if ($record->isSuperAdmin()) {
                            return 'All sections';
                        }

                        $labels = AdminPermissions::labels();
                        $keys = is_array($state) ? $state : [];

                        return collect($keys)
                            ->map(fn ($key) => $labels[$key] ?? $key)
                            ->take(3)
                            ->implode(', ')
                            . (count($keys) > 3 ? '…' : '');
                    })
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_admin')->boolean()->label('Admin'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records): void {
                            if ($records->contains(fn (User $user): bool => $user->isSuperAdmin()) && User::query()->where('is_super_admin', true)->count() <= $records->where('is_super_admin', true)->count()) {
                                throw new \RuntimeException('At least one super admin must remain.');
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
