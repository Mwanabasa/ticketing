@extends('layouts.app')
@section('title', 'Knowledge Base')
@section('page_title', 'Knowledge Base')
@section('page_subtitle', 'Publish articles to help students self-serve')

@section('content')

{{-- Page header --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6 bg-white rounded-2xl border border-gray-100 px-6 py-4 shadow-sm">
    <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white shrink-0"
             style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 12px rgba(79,70,229,0.3);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div>
            <h2 class="font-bold text-gray-900 text-base">All Articles</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                <span class="font-semibold text-indigo-600">{{ $articles->total() }}</span>
                article{{ $articles->total() !== 1 ? 's' : '' }} total ·
                <span class="text-emerald-600 font-semibold">{{ $articles->getCollection()->where('is_published', true)->count() }} published</span>
            </p>
        </div>
    </div>
    <a href="{{ route('admin.knowledge-base.create') }}"
       class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Article
    </a>
</div>

@if ($articles->isEmpty())
    {{-- Empty state --}}
    <div class="empty-state animate-fade-up">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-6"
             style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">No articles yet</h3>
        <p class="text-sm text-gray-400 max-w-sm mx-auto mb-8 leading-relaxed">
            Create your first knowledge base article to help students solve common IT issues on their own.
        </p>
        <a href="{{ route('admin.knowledge-base.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Write first article
        </a>
    </div>
@else
    {{-- Article grid --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($articles as $article)
            <div class="card group flex flex-col transition-all duration-200 hover:-translate-y-1 hover:shadow-lg animate-fade-up">
                <div class="p-5 flex-1">
                    {{-- Status badge --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                             style="{{ $article->is_published ? 'background:linear-gradient(135deg,#ede9fe,#ddd6fe);' : 'background:#f3f4f6;' }}">
                            <svg class="w-5 h-5 {{ $article->is_published ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        @if ($article->is_published)
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Draft
                            </span>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-900 text-sm leading-snug mb-2 group-hover:text-indigo-700 transition-colors">
                        {{ $article->title }}
                    </h3>

                    <p class="text-xs text-gray-400 leading-relaxed line-clamp-2 mb-3">
                        {{ Str::limit(strip_tags($article->content), 110) }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($article->category)
                            <span class="inline-flex rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-600">
                                {{ $article->category->name }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1 text-xs text-gray-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ number_format($article->views) }}
                        </span>
                        <span class="text-xs text-gray-300">·</span>
                        <span class="text-xs text-gray-400">{{ $article->created_at->format('M j, Y') }}</span>
                    </div>
                </div>

                {{-- Card footer --}}
                <div class="border-t border-gray-50 px-5 py-3 flex items-center justify-between bg-gray-50/50 rounded-b-2xl">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.knowledge-base.edit', $article) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        @if ($article->is_published)
                            <a href="{{ route('knowledge-base.show', $article) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-sky-500 hover:text-sky-700 transition-colors">
                                View live
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.knowledge-base.destroy', $article) }}"
                          onsubmit="return confirm('Delete this article permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-400 hover:text-red-600 transition-colors">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-5">{{ $articles->links() }}</div>
@endif

@endsection
