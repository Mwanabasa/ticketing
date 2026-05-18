@extends('layouts.app')

@section('title', 'Categories')
@section('page_title', 'Categories')

@section('content')
    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Create new category --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-4">Add new category</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex gap-3">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Network Issues"
                       class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('name') border-red-400 @enderror">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Add
                </button>
            </form>
            @error('name') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Category list --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">All categories</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $categories->count() }} categories total</p>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <li class="flex items-center justify-between gap-4 px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900">{{ $category->name }}</p>
                            <p class="text-xs text-slate-400">{{ $category->tickets_count }} {{ Str::plural('ticket', $category->tickets_count) }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Inline rename --}}
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                                  class="flex items-center gap-2" id="rename-form-{{ $category->id }}">
                                @csrf @method('PATCH')
                                <input type="text" name="name" value="{{ $category->name }}"
                                       class="hidden w-36 rounded-lg border border-slate-300 px-2 py-1 text-sm focus:border-indigo-400 focus:outline-none"
                                       id="rename-input-{{ $category->id }}">
                                <button type="submit" class="hidden text-xs font-semibold text-indigo-600 hover:underline"
                                        id="rename-save-{{ $category->id }}">Save</button>
                            </form>
                            <button type="button"
                                    onclick="toggleRename({{ $category->id }})"
                                    id="rename-btn-{{ $category->id }}"
                                    class="text-xs font-medium text-slate-500 hover:text-indigo-600 transition">
                                Rename
                            </button>
                            @if ($category->tickets_count === 0)
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Delete category {{ addslashes($category->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-500 hover:underline">Delete</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-300" title="Cannot delete — has tickets">Delete</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-slate-400">No categories yet.</li>
                @endforelse
            </ul>
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
