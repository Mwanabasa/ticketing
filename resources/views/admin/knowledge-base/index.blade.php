@extends('layouts.app')
@section('title', 'Knowledge Base')
@section('page_title', 'Knowledge Base')
@section('page_subtitle', 'Publish articles to help students solve common issues')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">All Articles</p>
                <p class="text-xs text-gray-400">{{ $articles->total() }} article{{ $articles->total() !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <a href="{{ route('admin.knowledge-base.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:-translate-y-0.5"
           style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Article
        </a>
    </div>

    @if ($articles->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-20 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-sky-50 mb-4">
                <svg class="w-8 h-8 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p class="font-bold text-gray-700 text-lg">No articles yet</p>
            <p class="mt-1 text-sm text-gray-400">Create your first article to help students self-serve.</p>
            <a href="{{ route('admin.knowledge-base.create') }}" class="mt-5 inline-flex rounded-xl px-6 py-2.5 text-sm font-bold text-white shadow-md" style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">Write first article</a>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($articles as $article)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow group flex flex-col">
                    <div class="p-5 flex-1">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $article->is_published ? 'bg-sky-50' : 'bg-gray-100' }}">
                                <svg class="w-5 h-5 {{ $article->is_published ? 'text-sky-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            @if ($article->is_published)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Draft
                                </span>
                            @endif
                        </div>

                        <h3 class="font-bold text-gray-900 mb-1 leading-snug">{{ $article->title }}</h3>

                        <div class="flex items-center gap-3 mt-2">
                            @if ($article->category)
                                <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-600">{{ $article->category->name }}</span>
                            @endif
                            <span class="flex items-center gap-1 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ number_format($article->views) }} views
                            </span>
                            <span class="text-xs text-gray-400">{{ $article->created_at->format('M j, Y') }}</span>
                        </div>

                        <p class="text-xs text-gray-400 mt-3 line-clamp-2">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                    </div>

                    <div class="border-t border-gray-100 px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.knowledge-base.edit', $article) }}"
                               class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            @if ($article->is_published)
                                <a href="{{ route('knowledge-base.show', $article) }}" target="_blank"
                                   class="text-sm font-semibold text-sky-500 hover:text-sky-600 transition">View ↗</a>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.knowledge-base.destroy', $article) }}" onsubmit="return confirm('Delete this article?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-red-400 hover:text-red-600 transition">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $articles->links() }}</div>
    @endif

@endsection
