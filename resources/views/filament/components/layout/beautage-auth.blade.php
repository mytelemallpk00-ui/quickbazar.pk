@php
    $livewire ??= null;
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="beautage-auth-layout fi-body min-h-screen w-full">
        {{ $slot }}
    </div>
</x-filament-panels::layout.base>
