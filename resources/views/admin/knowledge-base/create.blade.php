@extends('layouts.app')

@section('title', 'New article')
@section('page_title', 'New article')

@section('content')
    <div class="max-w-2xl">
        <p class="mb-6 text-sm text-slate-500">
            <a href="{{ route('admin.knowledge-base.index') }}" class="font-medium text-indigo-600 hover:underline">← Knowledge Base</a>
        </p>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.knowledge-base.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1.5">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required
                        placeholder="e.g. How to connect to campus WiFi"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('title') border-red-400 @enderror">
                    @error('title') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-slate-400">(optional)</span></label>
                    <select id="category_id" name="category_id"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="">No category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="content" class="block text-sm font-medium text-slate-700 mb-1.5">Content</label>
                    <textarea id="content" name="content" rows="12" required
                        placeholder="Write the article content here. You can use plain text or step-by-step instructions."
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('content') border-red-400 @enderror">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <input id="is_published" name="is_published" type="checkbox" value="1" @checked(old('is_published'))
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <label for="is_published" class="text-sm font-medium text-slate-700">Publish immediately</label>
                        <p class="text-xs text-slate-400">Students can see published articles on the Knowledge Base page.</p>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                        Save article
                    </button>
                    <a href="{{ route('admin.knowledge-base.index') }}"
                       class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
