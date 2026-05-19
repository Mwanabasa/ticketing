@extends('layouts.app')
@section('title', 'Knowledge Base')
@section('page_title', 'Knowledge Base')
@section('page_subtitle', 'Find answers before submitting a ticket')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Hero search --}}
    <div class="relative rounded-3xl overflow-hidden mb-10 p-8 text-center"
         style="background: linear-gradient(135deg, #0f0c29 0%, #1e1b4b 40%, #312e81 100%); box-shadow: 0 8px 32px rgba(79,70,229,0.3);">
        <div class="absolute inset-0 opacity-[0.06]"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 px-3 py-1 text-xs font-bold text-indigo-200 uppercase tracking-widest mb-4">
                📚 Help Center
            </div>
            <h2 class="text-2xl font-extrabold text-white mb-2 tracking-tight">How can we help you?</h2>
            <p class="text-indigo-300 text-sm mb-6">Search our knowledge base for instant answers.</p>
            <form method="GET" action="{{ route('knowledge-base.index') }}" class="flex gap-2 max-w-xl mx-auto">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input name="q" type="search" value="{{ request('q') }}" placeholder="Search articles…"
                           autofocus
                           class="w-full pl-11 pr-4 py-3 rounded-2xl text-sm font-medium outline-none border-2 border-transparent bg-white text-slate-900 focus:border-indigo-400 focus:shadow-[0_0_0_4px_rgba(99,102,241,0.15)] transition-all">
                </div>
                <button type="submit"
                        class="rounded-2xl px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 4px 14px rgba(99,102,241,0.4);">
                    Search
                </button>
            </form>
        </div>
    </div>

    {{-- Category filter pills --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('knowledge-base.index', request()->except('category_id', 'page')) }}"
           class="rounded-full px-4 py-1.5 text-xs font-semibold transition-all
                  {{ !request('category_id') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600' }}">
            All
        </a>
        @foreach ($categories as $category)
            <a href="{{ route('knowledge-base.index', array_merge(request()->except('page'), ['category_id' => $category->id])) }}"
               class="rounded-full px-4 py-1.5 text-xs font-semibold transition-all
                      {{ (string)request('category_id') === (string)$category->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600' }}">
                {{ $category->name }}
            </a>
        @endforeach
        @if (request('q') || request('category_id'))
            <a href="{{ route('knowledge-base.index') }}"
               class="rounded-full px-4 py-1.5 text-xs font-semibold bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition-all ml-auto">
                ✕ Clear filters
            </a>
        @endif
    </div>

    {{-- Results count --}}
    @if (request('q'))
        <p class="text-sm text-slate-500 mb-5">
            {{ $articles->total() }} result{{ $articles->total() !== 1 ? 's' : '' }} for
            <span class="font-semibold text-slate-800">"{{ request('q') }}"</span>
        </p>
    @endif

    {{-- Articles grid --}}
    @forelse ($articles as $article)
        <a href="{{ route('knowledge-base.show', $article) }}"
           class="group flex items-start gap-5 mb-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-lg hover:border-indigo-200 hover:-translate-y-0.5 transition-all duration-200">

            {{-- Icon --}}
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                @if ($article->category)
                    <span class="inline-block rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-2">
                        {{ $article->category->name }}
                    </span>
                @endif
                <h2 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors leading-snug mb-1">
                    {{ $article->title }}
                </h2>
                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                    {{ Str::limit(strip_tags($article->content), 160) }}
                </p>
            </div>

            <div class="flex flex-col items-end gap-2 shrink-0">
                <span class="text-[10px] text-slate-400 font-medium">{{ $article->views }} views</span>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-500 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white py-20 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="font-bold text-slate-700 text-lg">No articles found</p>
            <p class="mt-1 text-sm text-slate-400">Try a different search term or browse all categories.</p>
            <a href="{{ route('knowledge-base.index') }}" class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                Browse all articles
            </a>
        </div>
    @endforelse

    <div class="mt-6">{{ $articles->links() }}</div>
</div>
@endsection
