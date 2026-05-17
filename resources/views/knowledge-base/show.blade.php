@extends('layouts.app')

@section('title', $article->title)
@section('page_title', $article->title)

@section('content')
    <div class="max-w-3xl mx-auto">
        <p class="mb-6 text-sm">
            <a href="{{ route('knowledge-base.index') }}" class="font-medium text-indigo-600 hover:underline">← Knowledge Base</a>
        </p>

        <article class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="border-b border-slate-100 pb-5 mb-6">
                @if ($article->category)
                    <span class="inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 mb-3">{{ $article->category->name }}</span>
                @endif
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $article->title }}</h1>
                <p class="mt-2 text-xs text-slate-400">{{ $article->views }} views · Updated {{ $article->updated_at->format('M j, Y') }}</p>
            </div>
            <div class="prose prose-sm max-w-none text-slate-800 leading-relaxed">
                {!! nl2br(e($article->content)) !!}
            </div>
        </article>

        @if ($relatedArticles->isNotEmpty())
            <section class="mt-10">
                <h2 class="text-base font-bold text-slate-900 mb-4">Related articles</h2>
                <div class="space-y-3">
                    @foreach ($relatedArticles as $related)
                        <a href="{{ route('knowledge-base.show', $related) }}"
                           class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
                            <div>
                                <p class="font-semibold text-slate-900 hover:text-indigo-600">{{ $related->title }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ Str::limit(strip_tags($related->content), 100) }}</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @auth
            @if (auth()->user()->isStudent())
                <div class="mt-10 rounded-2xl border border-indigo-100 bg-indigo-50 p-5 text-center">
                    <p class="text-sm font-semibold text-indigo-900">Didn't find what you were looking for?</p>
                    <p class="mt-1 text-xs text-indigo-600">Submit a support ticket and our staff will help you.</p>
                    <a href="{{ route('student.tickets.create') }}" class="mt-3 inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Open a ticket
                    </a>
                </div>
            @endif
        @endauth
    </div>
@endsection
