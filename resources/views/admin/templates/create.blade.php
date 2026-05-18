@extends('layouts.app')
@section('title', 'New Template')
@section('page_title', 'New Template')
@section('page_subtitle', 'Create a reusable template for student tickets')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.templates.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-indigo-600 transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Templates
    </a>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #f8faff, #f3f0ff);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">New Template</h2>
                    <p class="text-xs text-gray-400">Fill in the details below</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.templates.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Template name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           placeholder="e.g. WiFi Connection Issue"
                           class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-gray-400 font-normal">(optional)</span></label>
                    <select id="category_id" name="category_id"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 transition">
                        <option value="">No category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required
                       placeholder="Pre-filled subject line for the ticket"
                       class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition {{ $errors->has('subject') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="7" required
                          placeholder="Pre-filled description that guides the student…"
                          class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition resize-none {{ $errors->has('description') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 rounded-xl border-2 border-gray-100 bg-gray-50 px-4 py-3.5">
                <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true))
                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <div>
                    <label for="is_active" class="text-sm font-semibold text-gray-700 cursor-pointer">Active template</label>
                    <p class="text-xs text-gray-400">Active templates appear as quick-fill buttons on the student ticket form.</p>
                </div>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="rounded-xl px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    Create Template
                </button>
                <a href="{{ route('admin.templates.index') }}"
                   class="rounded-xl border-2 border-gray-200 px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
