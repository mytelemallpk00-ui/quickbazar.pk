<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Throwable;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('fulfill')
                ->label('Mark as fulfilled')
                ->icon('heroicon-o-truck')
                ->color('success')
                ->visible(fn (Order $record): bool => $record->fulfillment_status === Order::FULFILLMENT_UNFULFILLED)
                ->action(function (Order $record): void {
                    $record->markFulfilled();
                    Notification::make()->title('Fulfilled')->success()->send();
                }),
            Actions\Action::make('unfulfill')
                ->label('Mark as unfulfilled')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (Order $record): bool => $record->fulfillment_status === Order::FULFILLMENT_FULFILLED)
                ->action(function (Order $record): void {
                    $record->markUnfulfilled();
                    Notification::make()->title('Unfulfilled')->success()->send();
                }),
            Actions\Action::make('delivered')
                ->label('Mark as delivered')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn (Order $record): bool => $record->status !== Order::STATUS_COMPLETED)
                ->action(function (Order $record): void {
                    $record->markDelivered();
                    Notification::make()->title('Delivered')->success()->send();
                }),
            Actions\Action::make('return')
                ->label('Mark as returned')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (Order $record): void {
                    $record->markReturned();
                    Notification::make()->title('Returned')->success()->send();
                }),
            Actions\Action::make('send_invoice')
                ->label('Send invoice')
                ->icon('heroicon-o-envelope')
                ->action(function (Order $record): void {
                    try {
                        OrderResource::sendInvoice($record);
                        Notification::make()->title('Invoice emailed to customer')->success()->send();
                    } catch (Throwable $e) {
                        report($e);
                        Notification::make()
                            ->title('Could not send email')
                            ->body('Order saved — configure mail on server or resend later.')
                            ->warning()
                            ->send();
                    }
                }),
            Actions\Action::make('mark_paid')
                ->label('Mark as paid')
                ->icon('heroicon-o-banknotes')
                ->visible(fn (Order $record): bool => $record->payment_status === Order::PAYMENT_PENDING)
                ->action(function (Order $record): void {
                    $record->update(['payment_status' => Order::PAYMENT_PAID]);
                    Notification::make()->title('Payment marked as paid')->success()->send();
                }),
            Actions\Action::make('cancel')
                ->label('Cancel order')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Order $record): bool => $record->status === Order::STATUS_OPEN)
                ->action(function (Order $record): void {
                    $record->cancelOrder();
                    Notification::make()->title('Order cancelled')->success()->send();
                }),
        ];
    }
}
