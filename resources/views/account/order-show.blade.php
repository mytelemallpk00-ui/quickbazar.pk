@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' — Beautage')

@section('content')
<main class="w-full flex-1 py-8 px-4 sm:px-12 xl:px-20 space-y-6 max-w-3xl mx-auto">
    <a href="{{ route('account.index') }}" class="text-xs font-bold text-beautage-pink hover:underline">← All orders</a>

    <div class="bg-white rounded-3xl border border-purple-100 p-6 shadow-xl space-y-4">
        <div class="flex flex-wrap justify-between gap-3">
            <div>
                <h1 class="text-xl font-serif font-bold text-beautage-purple">{{ $order->order_number }}</h1>
                <p class="text-xs text-slate-500">{{ $order->created_at?->format('M j, Y g:i A') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-purple-50 text-beautage-purple text-[10px] font-extrabold uppercase">{{ $order->status }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <p class="text-slate-400 font-bold uppercase">Payment</p>
                <p class="text-slate-800">{{ $order->payment_method }}</p>
                <p class="text-slate-500 capitalize">{{ $order->payment_status }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase">Delivery</p>
                <p class="text-slate-800">{{ $order->shipping_address_line }}</p>
                <p class="text-slate-500">{{ $order->shipping_city }}</p>
            </div>
        </div>

        @if ($order->tracking_number)
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4">
                <p class="text-xs font-bold text-emerald-800 uppercase">Tracking</p>
                <p class="text-sm font-mono font-bold text-emerald-900 mt-1">{{ $order->courier_label }} — {{ $order->tracking_number }}</p>
                <a href="{{ route('track-order') }}?q={{ urlencode($order->tracking_number) }}" class="inline-block mt-2 text-xs font-bold text-beautage-pink hover:underline">Track shipment</a>
            </div>
        @elseif ($order->courier_booking_status === 'pending')
            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-3">Courier booking in progress — tracking ID jald update hogi.</p>
        @endif

        <div class="border-t border-purple-100 pt-4 space-y-3">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-xs gap-3">
                    <span class="text-slate-700">{{ $item->name }} × {{ $item->quantity }}</span>
                    <span class="font-bold">Rs. {{ number_format($item->line_total, 0) }}</span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-purple-100 pt-4 space-y-1 text-xs">
            <div class="flex justify-between"><span>Subtotal</span><span>Rs. {{ number_format($order->subtotal, 0) }}</span></div>
            @if ($order->discount_total > 0)
                <div class="flex justify-between text-emerald-600"><span>Discount @if($order->promo_code)({{ $order->promo_code }})@endif</span><span>- Rs. {{ number_format($order->discount_total, 0) }}</span></div>
            @endif
            <div class="flex justify-between"><span>Shipping</span><span>Rs. {{ number_format($order->shipping_total, 0) }}</span></div>
            <div class="flex justify-between"><span>Tax</span><span>Rs. {{ number_format($order->tax_total, 0) }}</span></div>
            <div class="flex justify-between text-sm font-black text-beautage-purple pt-2"><span>Total</span><span>Rs. {{ number_format($order->total, 0) }}</span></div>
        </div>
    </div>
</main>
@endsection
