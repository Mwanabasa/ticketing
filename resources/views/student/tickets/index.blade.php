@extends('layouts.app')
@section('title', 'My tickets')
@section('page_title', 'My Tickets')
@section('page_subtitle', 'Track and manage your support requests')

@section('content')

{{-- Filter bar --}}
<div class="card p-4 mb-5 animate-fade-up">
    <form method="GET" action="{{ route('student.tickets.index') }}" class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tickets…"
                   class="input pl-9 py-2 text-sm">
        </div>
        <select name="status" class="input px-3 py-2 text-sm w-auto">
            <option value="">Active tickets</option>
            @foreach (\App\Enums\TicketStatus::cases() as $s)
                <option value="{{ $s->value }}" @selected(request('status') == $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>
        <select name="category_id" class="input px-3 py-2 text-sm w-auto">
            <option value="">All categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary py-2 px-4 text-sm">Filter</button>
        @if (request()->hasAny(['q','status','category_id']))
            <a href="{{ route('student.tickets.index') }}" class="btn btn-secondary py-2 px-4 text-sm">Clear</a>
        @endif
        <a href="{{ route('student.tickets.create') }}" class="btn btn-primary ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New ticket
        </a>
    </form>

    @unless (request()->filled('status'))
        <p class="mt-2 text-xs text-gray-400">
            Showing active tickets only.
            <a href="{{ route('student.tickets.index', array_merge(request()->except('page'), ['status' => 'closed'])) }}"
               class="text-indigo-500 hover:text-indigo-700 font-semibold transition-colors">Show closed tickets →</a>
        </p>
    @endunless
</div>

@if ($tickets->isEmpty())
    <div class="empty-state animate-fade-up">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-6"
             style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">No tickets found</h3>
        <p class="text-sm text-gray-400 max-w-sm mx-auto mb-8 leading-relaxed">
            {{ request()->hasAny(['q','status','category_id']) ? 'Try adjusting your filters.' : 'Submit a ticket whenever you need IT support.' }}
        </p>
        @unless (request()->hasAny(['q','status','category_id']))
            <a href="{{ route('student.tickets.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create your first ticket
            </a>
        @endunless
    </div>
@else
    <div class="card overflow-hidden animate-fade-up">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Ticket</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Priority</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Due</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($tickets as $ticket)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('student.tickets.show', $ticket) }}"
                               class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors group-hover:text-indigo-600">
                                <span class="text-xs font-mono text-gray-400 mr-1">#{{ $ticket->id }}</span>
                                {{ Str::limit($ticket->subject, 48) }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $ticket->category->name }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">
                                {{ $ticket->priority->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                                {{ $ticket->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs">
                            @if ($ticket->due_at && ! in_array($ticket->status, [\App\Enums\TicketStatus::Resolved, \App\Enums\TicketStatus::Closed]))
                                <span class="{{ $ticket->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                                    {{ $ticket->due_at->format('M j') }}
                                    @if ($ticket->isOverdue()) ⚠ @endif
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-400">{{ $ticket->updated_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-gray-100 px-5 py-4">{{ $tickets->links() }}</div>
    </div>
@endif
@endsection
