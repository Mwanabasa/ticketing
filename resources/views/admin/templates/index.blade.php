@extends('layouts.app')
@section('title', 'Ticket Templates')
@section('page_title', 'Ticket Templates')
@section('page_subtitle', 'Reusable templates that pre-fill the student ticket form')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-6 bg-white rounded-2xl border border-gray-100 px-6 py-4 shadow-sm animate-fade-up">
    <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white shrink-0"
             style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 12px rgba(79,70,229,0.3);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <h2 class="font-bold text-gray-900 text-base">All Templates</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                <span class="font-semibold text-indigo-600">{{ $templates->total() }}</span> template{{ $templates->total() !== 1 ? 's' : '' }} ·
                <span class="text-emerald-600 font-semibold">{{ $templates->getCollection()->where('is_active', true)->count() }} active</span>
            </p>
        </div>
    </div>
    <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Template
    </a>
</div>

@if ($templates->isEmpty())
    <div class="empty-state animate-fade-up">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-6"
             style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">No templates yet</h3>
        <p class="text-sm text-gray-400 max-w-sm mx-auto mb-8 leading-relaxed">
            Create templates to help students fill tickets faster with pre-filled subjects and descriptions.
        </p>
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create first template
        </a>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($templates as $template)
            <div class="card group flex flex-col transition-all duration-200 hover:-translate-y-1 hover:shadow-lg animate-fade-up">
                <div class="p-5 flex-1">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                             style="{{ $template->is_active ? 'background:linear-gradient(135deg,#ede9fe,#ddd6fe);' : 'background:#f3f4f6;' }}">
                            <svg class="w-5 h-5 {{ $template->is_active ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        @if ($template->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-500 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                            </span>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-900 text-sm mb-1 group-hover:text-indigo-700 transition-colors">{{ $template->name }}</h3>
                    <p class="text-xs text-gray-500 mb-2 line-clamp-1">{{ $template->subject }}</p>

                    @if ($template->category)
                        <span class="inline-flex rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-600">
                            {{ $template->category->name }}
                        </span>
                    @endif

                    <p class="text-xs text-gray-400 mt-3 line-clamp-2 leading-relaxed">{{ Str::limit($template->description, 100) }}</p>
                </div>

                <div class="border-t border-gray-50 px-5 py-3 flex items-center justify-between bg-gray-50/50 rounded-b-2xl">
                    <a href="{{ route('admin.templates.edit', $template) }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" onsubmit="return confirm('Delete this template?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-400 hover:text-red-600 transition-colors">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-5">{{ $templates->links() }}</div>
@endif

@endsection
