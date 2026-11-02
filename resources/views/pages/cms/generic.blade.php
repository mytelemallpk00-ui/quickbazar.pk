@extends('layouts.app')

@section('title', ($page->meta_title ?: $page->title).' — Beautage')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white pb-36 md:pb-0 overflow-x-hidden"
@endsection

@section('content')
<main class="w-full flex-1 flex flex-col py-6">
    @include('partials.cms-hero', ['page' => $page])

    @if(filled($page->content_html))
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="prose max-w-none bg-white rounded-3xl border border-purple-100 p-6 sm:p-10">{!! $page->content_html !!}</div>
        </section>
    @endif
</main>
@endsection
