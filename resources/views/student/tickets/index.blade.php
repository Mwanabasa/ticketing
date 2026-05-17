@extends('layouts.app')

@section('title', 'My tickets')
@section('page_title', 'My tickets')

@section('content')
    {{-- Filter bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-5">
        <form method="GET" action="{{ route('student.tickets.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tickets…"
                class="flex-1 min-w-[180px] rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <select name="status" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">All statuses</option>
                @foreach (\App\Enums\TicketStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(request('status') == $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
            <select name="category_id" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">All categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Filter</button>
            @if (request('q') || request('status') || request('category_id'))
                <a href="{{ route('student.tickets.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Clear</a>
            @endif
            <a href="{{ route('student.tickets.create') }}" class="ml-auto rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">+ New ticket</a>
        </form>
    </div>

    @if ($tickets->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white py-16 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 mb-4">
                <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <p class="font-semibold text-slate-700">No tickets found</p>
            <p class="mt-1 text-sm text-slate-400">{{ request()->hasAny(['q','status','category_id']) ? 'Try adjusting your filters.' : 'Submit a ticket when you need IT support.' }}</p>
            @unless (request()->hasAny(['q','status','category_id']))
                <a href="{{ route('student.tickets.create') }}" class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Create your first ticket</a>
            @endunless
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Ticket</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Priority</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($tickets as $ticket)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('student.tickets.show', $ticket) }}" class="font-semibold text-slate-900 hover:text-indigo-600 hover:underline">
                                    #{{ $ticket->id }} — {{ Str::limit($ticket->subject, 48) }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $ticket->category->name }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">{{ $ticket->priority->label() }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">{{ $ticket->status->label() }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="border-t border-slate-200 p-4">{{ $tickets->links() }}</div>
        </div>
    @endif
@endsection
