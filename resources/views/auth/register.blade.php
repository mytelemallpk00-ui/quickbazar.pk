@extends('layouts.app')

@section('title', 'Create customer account — Quick Bazar')

@section('content')
<main class="w-full flex-1 py-10 px-4 sm:px-12 xl:px-20">
    <div class="max-w-lg mx-auto bg-white rounded-3xl border border-purple-100 shadow-xl p-8 space-y-6">
        <div class="text-center space-y-1">
            <h1 class="text-2xl font-serif font-bold text-beautage-purple">Create shopping account</h1>
            <p class="text-xs text-slate-500">For orders and checkout on the Quick Bazar store.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">First name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Last name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phone (optional)</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                <input type="password" name="password" required class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" required class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs">
            </div>
            <button type="submit" class="w-full py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl">Create account</button>
        </form>

        <p class="text-center text-xs text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-beautage-pink font-bold hover:underline">Customer sign in</a>
        </p>
    </div>
</main>
@endsection
