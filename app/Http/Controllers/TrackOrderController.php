<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', $request->input('q', '')));
        if ($query === '') {
            return response()->json(['success' => false, 'message' => 'Enter order number or tracking ID.'], 422);
        }

        $order = Order::query()
            ->where(function ($builder) use ($query): void {
                $builder->where('order_number', $query)
                    ->orWhere('tracking_number', $query);
                if (str_contains($query, '@')) {
                    $builder->orWhere('customer_email', $query);
                }
            })
            ->latest()
            ->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'No order found for this reference.'], 404);
        }

        if (str_contains($query, '@') && ! $request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Sign in to your account to view all orders for this email.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'fulfillment_status' => $order->fulfillment_status,
                'total' => $order->total,
                'discount_total' => $order->discount_total,
                'promo_code' => $order->promo_code,
                'courier' => $order->courier_label,
                'tracking_number' => $order->tracking_number,
                'courier_booking_status' => $order->courier_booking_status,
                'shipping_city' => $order->shipping_city,
                'created_at' => $order->created_at?->toIso8601String(),
            ],
        ]);
    }
}
