<?php

namespace App\Services\StoreAutomation;

use App\Models\AbandonedCart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReorderReminderSchedule;
use App\Support\StoreAutomationSettings;
use Illuminate\Support\Facades\Schema;

class OrderAutomationPlanner
{
    public function afterOrderPlaced(Order $order): void
    {
        $this->markCartRecovered($order);
        $this->scheduleReorderReminders($order);
    }

    protected function markCartRecovered(Order $order): void
    {
        if (! Schema::hasTable('abandoned_carts')) {
            return;
        }

        AbandonedCart::query()
            ->where('email', $order->customer_email)
            ->whereNull('recovered_at')
            ->update([
                'recovered_order_id' => $order->id,
                'recovered_at' => now(),
            ]);
    }

    protected function scheduleReorderReminders(Order $order): void
    {
        $config = StoreAutomationSettings::section('reorder');
        if (empty($config['enabled']) || ! Schema::hasTable('reorder_reminder_schedules')) {
            return;
        }

        $defaultDays = (int) ($config['default_cycle_days'] ?? 42);
        $sendBefore = (int) ($config['send_days_before'] ?? 5);

        foreach ($order->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = Product::query()->find($item->product_id);
            $cycle = (int) ($product?->reorder_cycle_days ?? $defaultDays);
            if ($cycle < 7) {
                $cycle = $defaultDays;
            }

            $dueAt = now()->addDays(max(1, $cycle - $sendBefore));

            ReorderReminderSchedule::query()->firstOrCreate(
                [
                    'order_item_id' => $item->id,
                ],
                [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'customer_email' => $order->customer_email,
                    'cycle_days' => $cycle,
                    'due_at' => $dueAt,
                ],
            );
        }
    }
}
