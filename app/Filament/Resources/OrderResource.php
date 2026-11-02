<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Resources\OrderResource\Pages;
use App\Support\AdminPermissions;
use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class OrderResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::ORDERS;
    }

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->where('status', Order::STATUS_OPEN)
            ->where('fulfillment_status', Order::FULFILLMENT_UNFULFILLED)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $count = static::getNavigationBadge();

        return $count
            ? "{$count} new unfulfilled order(s) need action"
            : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')->disabled(),
                        Forms\Components\Select::make('status')
                            ->options(Order::statusLabels())
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('payment_status')
                            ->options(Order::paymentStatusLabels())
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('fulfillment_status')
                            ->options(Order::fulfillmentStatusLabels())
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('payment_method')->disabled(),
                        Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Customer & shipping')
                    ->schema([
                        Forms\Components\TextInput::make('customer_first_name')->disabled(),
                        Forms\Components\TextInput::make('customer_last_name')->disabled(),
                        Forms\Components\TextInput::make('customer_email')->disabled(),
                        Forms\Components\TextInput::make('customer_phone')->disabled(),
                        Forms\Components\Textarea::make('shipping_address_line')->disabled()->columnSpanFull(),
                        Forms\Components\TextInput::make('shipping_city')->disabled(),
                        Forms\Components\TextInput::make('shipping_postal')->disabled(),
                        Forms\Components\TextInput::make('shipping_method_id')->label('Shipping method ID')->disabled(),
                    ])->columns(2),
                Forms\Components\Section::make('Courier & tracking')
                    ->schema([
                        Forms\Components\TextInput::make('courier_label')->disabled(),
                        Forms\Components\TextInput::make('tracking_number')->label('Tracking ID'),
                        Forms\Components\TextInput::make('courier_booking_ref')->disabled(),
                        Forms\Components\TextInput::make('courier_booking_status')->disabled(),
                        Forms\Components\Textarea::make('courier_error')->disabled()->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Totals')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')->prefix('Rs')->disabled(),
                        Forms\Components\TextInput::make('discount_total')->prefix('Rs')->disabled(),
                        Forms\Components\TextInput::make('tax_total')->prefix('Rs')->disabled(),
                        Forms\Components\TextInput::make('shipping_total')->prefix('Rs')->disabled(),
                        Forms\Components\TextInput::make('total')->prefix('Rs')->disabled(),
                    ])->columns(3),
                Forms\Components\Section::make('Line items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')->disabled(),
                                Forms\Components\TextInput::make('quantity')->disabled(),
                                Forms\Components\TextInput::make('unit_price')->prefix('Rs')->disabled(),
                                Forms\Components\TextInput::make('line_total')->prefix('Rs')->disabled(),
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customerName')
                    ->label('Customer')
                    ->state(fn (Order $record): string => $record->customerName())
                    ->searchable(['customer_first_name', 'customer_last_name', 'customer_email']),
                Tables\Columns\TextColumn::make('total')
                    ->formatStateUsing(fn ($state): string => \App\Support\MoneyFormat::pkr($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::paymentStatusLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Order::PAYMENT_PAID => 'success',
                        Order::PAYMENT_REFUNDED => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Tracking')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('fulfillment_status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::fulfillmentStatusLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => $state === Order::FULFILLMENT_FULFILLED ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::statusLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_COMPLETED => 'success',
                        Order::STATUS_CANCELLED, Order::STATUS_RETURNED => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('fulfillment_status')
                    ->options(Order::fulfillmentStatusLabels()),
                Tables\Filters\SelectFilter::make('status')
                    ->options(Order::statusLabels()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('fulfill')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->fulfillment_status === Order::FULFILLMENT_UNFULFILLED)
                    ->action(function (Order $record): void {
                        $record->markFulfilled();
                        Notification::make()->title('Order marked as fulfilled')->success()->send();
                    }),
                Tables\Actions\Action::make('deliver')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status !== Order::STATUS_COMPLETED)
                    ->action(function (Order $record): void {
                        $record->markDelivered();
                        Notification::make()->title('Order marked as delivered')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function sendInvoice(Order $order): void
    {
        Mail::to($order->customer_email)->send(new OrderInvoiceMail($order));
        $order->markInvoiceSent();
    }
}
