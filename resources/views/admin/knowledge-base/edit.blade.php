@extends('layouts.app')
@section('title', 'Edit Article')
@section('page_title', 'Edit Article')
@section('page_subtitle', $article->title)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.knowledge-base.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-indigo-600 transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Knowledge Base
    </a>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #f0f9ff, #eef2ff);">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900">Editing Article</h2>
                        <p class="text-xs text-gray-400">{{ number_format($article->views) }} views · Created {{ $article->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
                @if ($article->is_published)
                    <a href="{{ route('knowledge-base.show', $article) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-600 hover:bg-sky-100 transition">
                        View live ↗
                    </a>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('admin.knowledge-base.update', $article) }}" class="p-6 space-y-5">
            @csrf @method('PATCH')

            <div class="grid sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Article title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $article->title) }}" required
                           class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-gray-400 font-normal">(optional)</span></label>
                    <select id="category_id" name="category_id"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 transition">
                        <option value="">No category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $article->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3 rounded-xl border-2 border-gray-100 bg-gray-50 px-4 py-3.5">
                    <input id="is_published" name="is_published" type="checkbox" value="1"
                           @checked(old('is_published', $article->is_published))
                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <label for="is_published" class="text-sm font-semibold text-gray-700 cursor-pointer">Published</label>
                        <p class="text-xs text-gray-400">Uncheck to save as draft.</p>
                    </div>
                </div>
            </div>

            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
                <textarea id="content" name="content" rows="14" required
                          class="w-full rounded-xl border-2 px-4 py-3 text-sm outline-none transition resize-none font-mono {{ $errors->has('content') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">{{ old('content', $article->content) }}</textarea>
                @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="rounded-xl px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                    Save Changes
                </button>
                <a href="{{ route('admin.knowledge-base.index') }}"
                   class="rounded-xl border-2 border-gray-200 px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border-2 border-red-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="font-bold text-red-800">Danger Zone</p>
                <p class="text-xs text-red-500">Permanently delete this article. This cannot be undone.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.knowledge-base.destroy', $article) }}" onsubmit="return confirm('Permanently delete this article?')">
            @csrf @method('DELETE')
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition">
                Delete Article
            </button>
        </form>
    </div>
</div>
@endsection
