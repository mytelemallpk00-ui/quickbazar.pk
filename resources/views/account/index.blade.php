@extends('layouts.app')

@section('title', 'My account — Quick Bazar')

@section('content')
<main class="w-full flex-1 py-8 px-4 sm:px-12 xl:px-20 space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-beautage-purple">My orders</h1>
            <p class="text-xs text-slate-500 mt-1">Hello, {{ $user->name }} — complete shopping history</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs font-bold text-slate-500 hover:text-beautage-purple">Sign out</button>
        </form>
    </div>

    @if (session('status'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-3">{{ session('status') }}</div>
    @endif

    <div class="space-y-4">
        @forelse ($orders as $order)
            <a href="{{ route('account.orders.show', $order) }}" class="block bg-white rounded-2xl border border-purple-100 p-5 shadow-sm hover:border-beautage-purple transition">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-mono font-bold text-beautage-pink">{{ $order->order_number }}</p>
                        <p class="text-sm font-bold text-beautage-purple mt-1">{{ $order->created_at?->format('M j, Y') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $order->items->count() }} item(s) · {{ $order->shipping_city }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-800">Rs. {{ number_format($order->total, 0) }}</p>
                        @if ($order->discount_total > 0)
                            <p class="text-[11px] text-emerald-600 font-semibold">Saved Rs. {{ number_format($order->discount_total, 0) }} @if($order->promo_code)({{ $order->promo_code }})@endif</p>
                        @endif
                        <p class="text-[11px] text-slate-500 mt-1 capitalize">{{ str_replace('_', ' ', $order->status) }}</p>
                    </div>
                </div>
                @if ($order->tracking_number)
                    <p class="text-[11px] text-slate-600 mt-3 font-mono">
                        {{ $order->courier_label ?? 'Courier' }} · {{ $order->tracking_number }}
                    </p>
                @endif
            </a>
        @empty
            <div class="text-center py-12 text-slate-500 text-sm">
                No orders yet. <a href="{{ route('home') }}" class="text-beautage-pink font-bold">Start shopping</a>
            </div>
        @endforelse
    </div>

    {{ $orders->links() }}
</main>
@endsection
