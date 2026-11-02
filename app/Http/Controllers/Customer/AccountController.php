<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $orders = Order::query()
            ->forAccount($user)
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('account.index', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $user = $request->user();
        abort_unless($order->belongsToAccount($user), 404);

        $order->load('items');

        return view('account.order-show', [
            'order' => $order,
        ]);
    }
}
