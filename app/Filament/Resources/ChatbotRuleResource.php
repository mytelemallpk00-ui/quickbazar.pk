<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Resources\ChatbotRuleResource\Pages;
use App\Models\ChatbotRule;
use App\Support\AdminPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatbotRuleResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $model = ChatbotRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Chatbot replies';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('keyword')
                ->required()
                ->maxLength(120)
                ->helperText('Customer message mein yeh word aaye to yeh reply bhejein (case-insensitive).'),
            Forms\Components\Textarea::make('response')
                ->required()
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('keyword')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('response')->limit(60),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListChatbotRules::route('/'),
            'create' => Pages\CreateChatbotRule::route('/create'),
            'edit' => Pages\EditChatbotRule::route('/{record}/edit'),
        ];
    }
}
