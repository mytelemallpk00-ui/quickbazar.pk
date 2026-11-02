@extends('layouts.app')

@section('title', ($page->meta_title ?: $page->title).' — Beautage')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white pb-36 md:pb-0 overflow-x-hidden"
@endsection

@section('content')
<main class="w-full flex-1 flex flex-col py-6">
    @include('partials.cms-hero', ['page' => $page])

    @php $tabs = $page->sections['tabs'] ?? []; @endphp
    @if(count($tabs))
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-10">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-2">
                @foreach($tabs as $index => $tab)
                    <button type="button" data-tab="{{ $tab['key'] ?? $index }}" class="size-tab-btn px-5 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider transition bg-white text-slate-700 border border-purple-200 shrink-0">
                        {{ $tab['label'] ?? 'Tab' }}
                    </button>
                @endforeach
            </div>
        </section>

        @foreach($tabs as $index => $tab)
            @php $tabKey = $tab['key'] ?? (string) $index; @endphp
            <div class="size-tab-panel hidden w-full px-4 sm:px-12 xl:px-20 mb-16 space-y-8" data-panel="{{ $tabKey }}">
                @if(! empty($tab['intro']))
                    <p class="text-sm text-slate-600">{{ $tab['intro'] }}</p>
                @endif
                @if(! empty($tab['rows']) && is_array($tab['rows']))
                    <div class="overflow-x-auto bg-white rounded-3xl border border-purple-100 shadow-sm">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-purple-900 text-white">
                                    @foreach(array_keys($tab['rows'][0] ?? []) as $heading)
                                        <th class="p-3.5 font-bold">{{ str($heading)->headline() }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tab['rows'] as $row)
                                    <tr class="border-b border-purple-50 odd:bg-purple-50/40">
                                        @foreach($row as $cell)
                                            <td class="p-3.5 text-slate-700">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if(! empty($tab['html']))
                    <div class="prose prose-sm max-w-none">{!! $tab['html'] !!}</div>
                @endif
            </div>
        @endforeach
    @elseif($page->content_html)
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="prose max-w-none bg-white rounded-3xl border border-purple-100 p-6 sm:p-10">{!! $page->content_html !!}</div>
        </section>
    @endif
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = Array.from(document.querySelectorAll('.size-tab-btn'));
    const panels = Array.from(document.querySelectorAll('.size-tab-panel'));
    if (!buttons.length) return;

    const activate = (key) => {
        buttons.forEach(btn => {
            const active = btn.dataset.tab === key;
            btn.classList.toggle('bg-beautage-purple', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('bg-white', !active);
            btn.classList.toggle('text-slate-700', !active);
        });
        panels.forEach(panel => panel.classList.toggle('hidden', panel.dataset.panel !== key));
    };

    buttons.forEach(btn => btn.addEventListener('click', () => activate(btn.dataset.tab)));
    activate(buttons[0].dataset.tab);
});
</script>
@endpush
