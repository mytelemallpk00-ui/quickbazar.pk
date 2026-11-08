@php
    $highlightDepartments = \App\Models\Category::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->limit(8)
        ->get();
@endphp
<section class="qb-highlights px-4 py-8 sm:px-6 lg:px-8" aria-labelledby="store-highlights-title">
    <div class="grid w-full gap-8 lg:grid-cols-12 lg:items-start">
        <div class="lg:col-span-5">
            <p class="qb-eyebrow">Shop Pakistan</p>
            <h2 id="store-highlights-title" class="mt-1 text-2xl font-extrabold text-slate-900">Shop with confidence</h2>
            <ol class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-1">
                <li class="flex items-start gap-3">
                    <span class="qb-step-no">1</span>
                    <span class="text-sm text-slate-600"><span class="font-extrabold text-slate-900">Choose a department.</span> Prices stay in PKR.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="qb-step-no">2</span>
                    <span class="text-sm text-slate-600"><span class="font-extrabold text-slate-900">Check out your way.</span> Cash on delivery is available.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="qb-step-no">3</span>
                    <span class="text-sm text-slate-600"><span class="font-extrabold text-slate-900">Track delivery.</span> Support stays on WhatsApp.</span>
                </li>
            </ol>
        </div>
        @if($highlightDepartments->isNotEmpty())
        <div class="lg:col-span-7">
            <p class="qb-eyebrow">Departments</p>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($highlightDepartments as $department)
                    <a href="{{ $department->collectionsUrl() }}" class="qb-card flex items-center gap-2 px-3 py-3 text-sm font-bold text-slate-800">
                        <i data-lucide="{{ $department->iconName() }}" class="h-4 w-4 shrink-0" style="color: var(--qb-orange)" aria-hidden="true"></i>
                        <span class="truncate">{{ $department->displayNavLabel() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
