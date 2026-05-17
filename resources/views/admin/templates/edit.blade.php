@extends('layouts.app')

@section('title', 'Edit template')
@section('page_title', 'Edit template')

@section('content')
    <div class="max-w-2xl">
        <p class="mb-5 text-sm"><a href="{{ route('admin.templates.index') }}" class="font-medium text-indigo-600 hover:underline">← Templates</a></p>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.templates.update', $template) }}" class="space-y-5">
                @csrf @method('PATCH')
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Template name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $template->name) }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('name') border-red-400 @enderror">
                    @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-slate-400">(optional)</span></label>
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="">No category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $template->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-slate-700 mb-1.5">Subject</label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject', $template->subject) }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('subject') border-red-400 @enderror">
                    @error('subject') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="7" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('description') border-red-400 @enderror">{{ old('description', $template->description) }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $template->is_active)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
                        <p class="text-xs text-slate-400">Uncheck to hide this template from students.</p>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Save changes</button>
                    <a href="{{ route('admin.templates.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                </div>
            </form>
        </div>

        <div class="mt-5 rounded-2xl border border-red-100 bg-red-50 p-5">
            <p class="text-sm font-semibold text-red-800 mb-3">Danger zone</p>
            <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" onsubmit="return confirm('Delete this template?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-600 hover:text-white transition">
                    Delete template
                </button>
            </form>
        </div>
    </div>
@endsection
