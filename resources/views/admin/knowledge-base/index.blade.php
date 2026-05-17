@extends('layouts.app')

@section('title', 'Knowledge Base')
@section('page_title', 'Knowledge Base')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-slate-500">Publish articles students can read to solve common issues themselves.</p>
        <a href="{{ route('admin.knowledge-base.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            New article
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Title</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Views</th>
                    <th class="px-5 py-3 text-left">Created</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($articles as $article)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <a href="{{ route('knowledge-base.show', $article) }}" target="_blank"
                               class="font-medium text-slate-900 hover:text-indigo-600 hover:underline">
                                {{ $article->title }}
                            </a>
                        </td>
                        <td class="px-5 py-4 text-slate-500">{{ $article->category?->name ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if ($article->is_published)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-500">{{ number_format($article->views) }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ $article->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.knowledge-base.edit', $article) }}"
                                   class="text-sm font-medium text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.knowledge-base.destroy', $article) }}"
                                      onsubmit="return confirm('Delete this article?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <p class="font-medium text-slate-700">No articles yet.</p>
                            <p class="mt-1 text-sm text-slate-400">Create your first article to help students self-serve.</p>
                            <a href="{{ route('admin.knowledge-base.create') }}"
                               class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                New article
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-200 p-4">{{ $articles->links() }}</div>
    </div>
@endsection
