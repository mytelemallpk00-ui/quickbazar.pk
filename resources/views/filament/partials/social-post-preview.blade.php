<div class="space-y-3 text-sm">
    @if(filled($post->title))
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase">Title</p>
            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $post->title }}</p>
        </div>
    @endif
    <div>
        <p class="text-xs font-bold text-gray-500 uppercase">Caption</p>
        <p class="whitespace-pre-wrap text-gray-800 dark:text-gray-200">{{ $post->caption }}</p>
    </div>
    <div class="flex flex-wrap gap-2 text-xs">
        <span class="rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1">{{ $post->category }}</span>
        @foreach($post->platforms ?? [] as $platform)
            <span class="rounded-full bg-primary-50 text-primary-700 px-2 py-1">{{ $platform }}</span>
        @endforeach
    </div>
    @if(filled($post->error_log))
        <div class="rounded-lg bg-danger-50 border border-danger-200 p-3 text-danger-800 text-xs whitespace-pre-wrap">
            {{ $post->error_log }}
        </div>
    @endif
</div>
