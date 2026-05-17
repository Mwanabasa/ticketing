@extends('layouts.app')

@section('title', 'New template')
@section('page_title', 'New template')

@section('content')
    <div class="max-w-2xl">
        <p class="mb-5 text-sm"><a href="{{ route('admin.templates.index') }}" class="font-medium text-indigo-600 hover:underline">← Templates</a></p>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.templates.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Template name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required placeholder="e.g. WiFi Connection Issue"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('name') border-red-400 @enderror">
                    @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-slate-400">(optional)</span></label>
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="">No category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-slate-700 mb-1.5">Subject</label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('subject') border-red-400 @enderror">
                    @error('subject') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="7" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
                        <p class="text-xs text-slate-400">Active templates appear as buttons on the student ticket form.</p>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Create template</button>
                    <a href="{{ route('admin.templates.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
