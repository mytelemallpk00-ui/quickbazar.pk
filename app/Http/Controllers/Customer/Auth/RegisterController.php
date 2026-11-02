<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRegisterRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): RedirectResponse|View
    {
        if (Auth::check()) {
            return redirect()->route('account.index');
        }

        return view('auth.register');
    }

    public function store(StoreCustomerRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => trim($data['first_name'].' '.($data['last_name'] ?? '')),
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'is_admin' => false,
            'is_super_admin' => false,
            'admin_permissions' => [],
        ]);

        Order::query()
            ->whereNull('user_id')
            ->where('customer_email', $user->email)
            ->update(['user_id' => $user->id]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.index')->with('status', 'Welcome! Your past orders are linked to this account.');
    }
}
