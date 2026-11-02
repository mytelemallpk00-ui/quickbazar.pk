<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\CheckoutSettings;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        return view('checkout', [
            'productsCatalog' => Product::catalogForJs(),
            'checkoutConfig' => CheckoutSettings::forStorefront(),
            'prefillCustomer' => $user ? [
                'email' => $user->email,
                'first_name' => explode(' ', (string) $user->name)[0] ?? '',
                'last_name' => trim(substr((string) $user->name, strlen(explode(' ', (string) $user->name)[0] ?? ''))),
                'phone' => $user->phone,
                'address' => $user->address,
            ] : null,
        ]);
    }
}
