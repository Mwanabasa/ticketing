@extends('layouts.app')
@section('title', 'Categories')
@section('page_title', 'Categories')
@section('page_subtitle', 'Manage ticket categories')

@section('content')
<div class="grid gap-6 lg:grid-cols-2">

    {{-- Add category --}}
    <div class="card p-6 animate-fade-up">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0"
                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 12px rgba(79,70,229,0.3);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Add new category</h2>
                <p class="text-xs text-gray-400 mt-0.5">Categories help organise support tickets</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="flex gap-3">
            @csrf
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="e.g. Network Issues"
                   class="input flex-1 @error('name') border-red-400 @enderror">
            <button type="submit" class="btn btn-primary shrink-0">Add</button>
        </form>
        @error('name') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Category list --}}
    <div class="card overflow-hidden animate-fade-up">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-bold text-gray-900">All categories</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    <span class="font-semibold text-indigo-600">{{ $categories->count() }}</span> categories total
                </p>
            </div>
        </div>

        @if ($categories->isEmpty())
            <div class="py-12 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-50 mb-3">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">No categories yet</p>
                <p class="text-xs text-gray-400 mt-1">Add your first category above.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach ($categories as $category)
                    <li class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-gray-50/60 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 style="background:linear-gradient(135deg,#eef2ff,#ede9fe);">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 text-sm">{{ $category->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $category->tickets_count }} {{ Str::plural('ticket', $category->tickets_count) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Inline rename --}}
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                                  class="flex items-center gap-2" id="rename-form-{{ $category->id }}">
                                @csrf @method('PATCH')
                                <input type="text" name="name" value="{{ $category->name }}"
                                       class="hidden input w-32 py-1 text-xs"
                                       id="rename-input-{{ $category->id }}">
                                <button type="submit"
                                        class="hidden text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors"
                                        id="rename-save-{{ $category->id }}">Save</button>
                            </form>
                            <button type="button"
                                    onclick="toggleRename({{ $category->id }})"
                                    id="rename-btn-{{ $category->id }}"
                                    class="text-xs font-semibold text-gray-400 hover:text-indigo-600 transition-colors">
                                Rename
                            </button>
                            @if ($category->tickets_count === 0)
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Delete category \'{{ addslashes($category->name) }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-400 hover:text-red-600 transition-colors">Delete</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-200 cursor-not-allowed" title="Cannot delete — has tickets">Delete</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@push('head')
<script>
function toggleRename(id) {
    const input  = document.getElementById('rename-input-' + id);
    const save   = document.getElementById('rename-save-' + id);
    const btn    = document.getElementById('rename-btn-' + id);
    const hidden = input.classList.contains('hidden');
    input.classList.toggle('hidden', !hidden);
    save.classList.toggle('hidden', !hidden);
    btn.textContent = hidden ? 'Cancel' : 'Rename';
    if (hidden) input.focus();
}
</script>
@endpush
@endsection
