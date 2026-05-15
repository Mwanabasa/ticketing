@extends('layouts.app')

@section('title', 'Staff dashboard')
@section('page_title', 'Staff dashboard')

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Staff dashboard</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Support queue overview</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                    Monitor incoming tickets, spot unassigned requests, and jump into queue management quickly.
                </p>
            </div>
            <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                Manage tickets
            </a>
        </div>
    </div>

    <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}" class="rounded-xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm hover:border-emerald-200">
            <dt class="text-sm font-medium text-emerald-800">Open</dt>
            <dd class="mt-3 text-3xl font-bold text-emerald-700">{{ $stats['open'] }}</dd>
            <p class="mt-2 text-xs text-emerald-700">New requests.</p>
        </a>
        <a href="{{ route('admin.tickets.index', ['status' => 'pending']) }}" class="rounded-xl border border-amber-100 bg-amber-50 p-5 shadow-sm hover:border-amber-200">
            <dt class="text-sm font-medium text-amber-800">Pending</dt>
            <dd class="mt-3 text-3xl font-bold text-amber-700">{{ $stats['pending'] }}</dd>
            <p class="mt-2 text-xs text-amber-700">In progress.</p>
        </a>
        <a href="{{ route('admin.tickets.index', ['assigned_to' => 'unassigned']) }}" class="rounded-xl border border-rose-100 bg-rose-50 p-5 shadow-sm hover:border-rose-200">
            <dt class="text-sm font-medium text-rose-800">Unassigned</dt>
            <dd class="mt-3 text-3xl font-bold text-rose-700">{{ $stats['unassigned'] }}</dd>
            <p class="mt-2 text-xs text-rose-700">Needs owner.</p>
        </a>
        <a href="{{ route('admin.tickets.index', ['assigned_to' => auth()->id()]) }}" class="rounded-xl border border-indigo-100 bg-indigo-50 p-5 shadow-sm hover:border-indigo-200">
            <dt class="text-sm font-medium text-indigo-800">Assigned to me</dt>
            <dd class="mt-3 text-3xl font-bold text-indigo-700">{{ $stats['assigned_to_me'] }}</dd>
            <p class="mt-2 text-xs text-indigo-700">Your active queue.</p>
        </a>
        <a href="{{ route('admin.tickets.index', ['status' => 'resolved']) }}" class="rounded-xl border border-blue-100 bg-blue-50 p-5 shadow-sm hover:border-blue-200">
            <dt class="text-sm font-medium text-blue-800">Resolved</dt>
            <dd class="mt-3 text-3xl font-bold text-blue-700">{{ $stats['resolved'] }}</dd>
            <p class="mt-2 text-xs text-blue-700">Ready to close.</p>
        </a>
        <a href="{{ route('admin.tickets.index', ['status' => 'closed']) }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-slate-300">
            <dt class="text-sm font-medium text-slate-500">Closed</dt>
            <dd class="mt-3 text-3xl font-bold text-slate-700">{{ $stats['closed'] }}</dd>
            <p class="mt-2 text-xs text-slate-500">Completed.</p>
        </a>
    </dl>

    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Latest tickets</h2>
                <p class="mt-1 text-sm text-slate-500">Most recent queue activity across all students.</p>
            </div>
            <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                View all
            </a>
        </div>

        <ul class="divide-y divide-slate-100">
            @forelse ($recentTickets as $ticket)
                <li class="grid gap-3 px-5 py-4 hover:bg-slate-50/80 sm:grid-cols-[1fr_auto] sm:items-center">
                    <div>
                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-semibold text-slate-950 hover:underline">
                            #{{ $ticket->id }} - {{ Str::limit($ticket->subject, 64) }}
                        </a>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $ticket->user->name }} - {{ $ticket->category->name }} - {{ $ticket->assignee?->name ?? 'Unassigned' }}
                        </p>
                    </div>
                    <span class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                        {{ $ticket->status->label() }}
                    </span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-slate-500">No tickets yet.</li>
            @endforelse
        </ul>
    </section>
@endsection
