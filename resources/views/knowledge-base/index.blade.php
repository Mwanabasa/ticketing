@extends('layouts.app')
@section('title', 'Knowledge Base')
@section('page_title', 'Knowledge Base')
@section('page_subtitle', 'Find answers to common IT issues')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900">Knowledge Base</h1>
            <p class="mt-2 text-slate-500">Find answers to common IT issues before submitting a ticket.</p>
        </div>

        <form method="GET" action="{{ route('knowledge-base.index') }}" class="mb-8 flex flex-wrap gap-3">
            <input name="q" type="search" value="{{ request('q') }}" placeholder="Search articles…"
                class="flex-1 min-w-[200px] rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <select name="category_id" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Search</button>
            @if (request('q') || request('category_id'))
                <a href="{{ route('knowledge-base.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Clear</a>
            @endif
        </form>

        @forelse ($articles as $article)
            <article class="mb-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-indigo-200 transition">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1">
                        @if ($article->category)
                            <span class="inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 mb-2">{{ $article->category->name }}</span>
                        @endif
                        <h2 class="text-base font-bold text-slate-900">
                            <a href="{{ route('knowledge-base.show', $article) }}" class="hover:text-indigo-600 hover:underline">{{ $article->title }}</a>
                        </h2>
                        <p class="mt-1.5 text-sm text-slate-500 line-clamp-2">{{ Str::limit(strip_tags($article->content), 160) }}</p>
                    </div>
                    <span class="text-xs text-slate-400 shrink-0">{{ $article->views }} views</span>
                </div>
                <a href="{{ route('knowledge-base.show', $article) }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:underline">
                    Read article →
                </a>
            </article>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white py-16 text-center shadow-sm">
                <p class="font-semibold text-slate-700">No articles found</p>
                <p class="mt-1 text-sm text-slate-400">Try a different search term or category.</p>
            </div>
        @endforelse

        <div class="mt-6">{{ $articles->links() }}</div>
    </div>
@endsection
