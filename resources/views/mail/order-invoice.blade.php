@php use App\Support\MoneyFormat; @endphp
<x-mail::message>
# Invoice {{ $order->order_number }}

Hello {{ $order->customerName() }},

Thank you for shopping with **Quick Bazar**. Here is a summary of your order.

**Payment method:** {{ $order->payment_method }}  
**Order total:** {{ MoneyFormat::pkr($order->total) }}

@component('mail::table')
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | {{ MoneyFormat::pkr($item->line_total) }} |
@endforeach
@endcomponent

**Subtotal:** {{ MoneyFormat::pkr($order->subtotal) }}  
@if($order->discount_total > 0)
**Discount:** -{{ MoneyFormat::pkr($order->discount_total) }}  
@endif
**Tax:** {{ MoneyFormat::pkr($order->tax_total) }}  
**Shipping:** {{ MoneyFormat::pkr($order->shipping_total) }}  
**Total:** **{{ MoneyFormat::pkr($order->total) }}**

<x-mail::button :url="url('/track-order')">
Track your order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
