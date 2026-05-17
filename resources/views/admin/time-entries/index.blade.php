@extends('layouts.app')

@section('title', 'Time entries — Ticket #'.$ticket->id)
@section('page_title', 'Time entries')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">
            <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-medium text-indigo-600 hover:underline">← Ticket #{{ $ticket->id }}</a>
            — {{ Str::limit($ticket->subject, 60) }}
        </p>
        <a href="{{ route('admin.time-entries.create', $ticket) }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            + Log time
        </a>
    </div>

    @php $totalMinutes = $timeEntries->sum('duration_minutes'); @endphp
    <div class="mb-5 rounded-2xl border border-indigo-100 bg-indigo-50 px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Total time logged</p>
            <p class="text-2xl font-extrabold text-indigo-900">
                {{ intdiv($totalMinutes, 60) }}h {{ $totalMinutes % 60 }}m
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Staff</th>
                    <th class="px-5 py-3 text-left">Description</th>
                    <th class="px-5 py-3 text-left">Duration</th>
                    <th class="px-5 py-3 text-left">Started at</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($timeEntries as $entry)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">
                                    {{ strtoupper(substr($entry->user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-900">{{ $entry->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">{{ $entry->description ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="font-semibold text-slate-900">{{ $entry->duration_minutes }}</span>
                            <span class="text-slate-400 text-xs"> min</span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">
                            {{ $entry->started_at ? \Carbon\Carbon::parse($entry->started_at)->format('M j, Y g:i A') : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <form method="POST" action="{{ route('admin.time-entries.destroy', $entry) }}" onsubmit="return confirm('Delete this entry?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <p class="font-semibold text-slate-700">No time entries yet</p>
                            <p class="mt-1 text-sm text-slate-400">Log time spent working on this ticket.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-200 p-4">{{ $timeEntries->links() }}</div>
    </div>
@endsection
