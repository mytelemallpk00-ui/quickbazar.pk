@php
    $url = $storeUrl ?? url('/');
@endphp
<div class="me-2 hidden shrink-0 sm:block">
    <x-filament::button
        tag="a"
        :href="$url"
        target="_blank"
        rel="noopener noreferrer"
        size="sm"
        color="gray"
        icon="heroicon-o-arrow-top-right-on-square"
        icon-alias="panels::topbar.visit-store"
    >
        Visit Store
    </x-filament::button>
</div>
