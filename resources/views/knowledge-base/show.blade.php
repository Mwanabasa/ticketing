@extends('layouts.app')
@section('title', $article->title)
@section('page_title', 'Knowledge Base')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-400 mb-6 font-medium">
        <a href="{{ route('knowledge-base.index') }}" class="hover:text-indigo-600 transition-colors">Knowledge Base</a>
        @if ($article->category)
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('knowledge-base.index', ['category_id' => $article->category_id]) }}"
               class="hover:text-indigo-600 transition-colors">{{ $article->category->name }}</a>
        @endif
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600 truncate max-w-xs">{{ $article->title }}</span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Main article --}}
        <div class="lg:col-span-2">
            <article class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                {{-- Article header --}}
                <div class="px-8 pt-8 pb-6 border-b border-slate-100"
                     style="background: linear-gradient(135deg, #f8faff, #f3f0ff);">
                    @if ($article->category)
                        <span class="inline-block rounded-full bg-indigo-100 border border-indigo-200 px-3 py-0.5 text-[10px] font-bold text-indigo-700 uppercase tracking-wider mb-3">
                            {{ $article->category->name }}
                        </span>
                    @endif
                    <h1 class="text-2xl font-extrabold text-slate-900 leading-tight tracking-tight mb-3">
                        {{ $article->title }}
                    </h1>
                    <div class="flex items-center gap-4 text-xs text-slate-400 font-medium">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ $article->views }} views
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Updated {{ $article->updated_at->format('M j, Y') }}
                        </span>
                    </div>
                </div>

                {{-- Article body --}}
                <div class="px-8 py-7">
                    <div class="prose prose-slate prose-sm max-w-none
                                prose-headings:font-bold prose-headings:text-slate-900
                                prose-p:text-slate-700 prose-p:leading-relaxed
                                prose-a:text-indigo-600 prose-a:no-underline hover:prose-a:underline
                                prose-code:bg-slate-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-indigo-700 prose-code:font-mono prose-code:text-xs
                                prose-pre:bg-slate-900 prose-pre:rounded-xl
                                prose-blockquote:border-indigo-400 prose-blockquote:bg-indigo-50 prose-blockquote:rounded-r-xl prose-blockquote:py-1
                                prose-li:text-slate-700">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            </article>

            {{-- Was this helpful? --}}
            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between gap-4 shadow-sm">
                <p class="text-sm font-semibold text-slate-700">Was this article helpful?</p>
                <div class="flex items-center gap-2">
                    <button class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>
                        Yes
                    </button>
                    <button class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:border-red-300 hover:bg-red-50 hover:text-red-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/></svg>
                        No
                    </button>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-4">

            {{-- Related articles --}}
            @if ($relatedArticles->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">Related articles</p>
                    <div class="space-y-2">
                        @foreach ($relatedArticles as $related)
                            <a href="{{ route('knowledge-base.show', $related) }}"
                               class="group flex items-start gap-3 rounded-xl p-3 hover:bg-indigo-50 transition-colors">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors leading-snug">{{ $related->title }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-1">{{ Str::limit(strip_tags($related->content), 60) }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Still need help? --}}
            @auth
                @if (auth()->user()->isStudent())
                    <div class="rounded-2xl overflow-hidden shadow-sm"
                         style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
                        <div class="p-5">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="text-sm font-bold text-white mb-1">Still need help?</p>
                            <p class="text-xs text-indigo-300 mb-4 leading-relaxed">Our support team is ready to assist you directly.</p>
                            <a href="{{ route('student.tickets.create') }}"
                               class="flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-indigo-700 hover:bg-indigo-50 transition-colors">
                                Open a ticket
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5 text-center">
                    <p class="text-sm font-semibold text-indigo-900 mb-1">Need more help?</p>
                    <p class="text-xs text-indigo-600 mb-3">Sign in to submit a support ticket.</p>
                    <a href="{{ route('login') }}" class="inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Sign in
                    </a>
                </div>
            @endauth
        </aside>
    </div>
</div>
@endsection
