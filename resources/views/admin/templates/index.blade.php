@extends('layouts.app')

@section('title', 'Ticket templates')
@section('page_title', 'Ticket templates')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Reusable templates that pre-fill the student ticket form.</p>
        <a href="{{ route('admin.templates.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            + New template
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-left">Subject</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($templates as $template)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $template->name }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ $template->category?->name ?? '—' }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ Str::limit($template->subject, 50) }}</td>
                        <td class="px-5 py-4">
                            @if ($template->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.templates.edit', $template) }}" class="text-sm font-medium text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" onsubmit="return confirm('Delete this template?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <p class="font-semibold text-slate-700">No templates yet</p>
                            <p class="mt-1 text-sm text-slate-400">Create templates to help students fill tickets faster.</p>
                            <a href="{{ route('admin.templates.create') }}" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">New template</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-200 p-4">{{ $templates->links() }}</div>
    </div>
@endsection
