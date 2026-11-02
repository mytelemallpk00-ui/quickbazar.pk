@extends('layouts.app')

@section('title', 'Customer sign in — Beautage')

@section('content')
<main class="w-full flex-1 py-10 px-4 sm:px-12 xl:px-20">
    <div class="max-w-md mx-auto bg-white rounded-3xl border border-purple-100 shadow-xl p-8 space-y-6">
        <div class="text-center space-y-1">
            <h1 class="text-2xl font-serif font-bold text-beautage-purple">Customer sign in</h1>
            <p class="text-xs text-slate-500">Orders, tracking, and your shopping account.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                <input type="password" name="password" required autocomplete="current-password" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-600">
                <input type="checkbox" name="remember" class="accent-beautage-pink">
                Remember me
            </label>
            <button type="submit" class="w-full py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl">Sign in</button>
        </form>

        <p class="text-center text-xs text-slate-500">
            New customer?
            <a href="{{ route('register') }}" class="text-beautage-pink font-bold hover:underline">Create shopping account</a>
        </p>
    </div>
</main>
@endsection
