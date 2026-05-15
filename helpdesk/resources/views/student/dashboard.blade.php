@extends('layouts.app')

@section('title', 'My dashboard')
@section('page_title', 'My dashboard')

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Student dashboard</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Welcome back, {{ auth()->user()->name }}.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                    Track your support requests, see who is assigned, and continue conversations from one place.
                </p>
            </div>
            <a href="{{ route('student.tickets.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                New support ticket
            </a>
        </div>
    </div>

    <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Active tickets</dt>
            <dd class="mt-3 text-3xl font-bold text-slate-950">{{ $activeCount }}</dd>
            <p class="mt-2 text-xs text-slate-500">Open and pending requests.</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
            <dt class="text-sm font-medium text-emerald-800">Open</dt>
            <dd class="mt-3 text-3xl font-bold text-emerald-700">{{ $stats['open'] }}</dd>
            <p class="mt-2 text-xs text-emerald-700">Waiting for support review.</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
            <dt class="text-sm font-medium text-amber-800">Pending</dt>
            <dd class="mt-3 text-3xl font-bold text-amber-700">{{ $stats['pending'] }}</dd>
            <p class="mt-2 text-xs text-amber-700">Support has responded or is working.</p>
        </div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-5 shadow-sm">
            <dt class="text-sm font-medium text-blue-800">Resolved</dt>
            <dd class="mt-3 text-3xl font-bold text-blue-700">{{ $stats['resolved'] }}</dd>
            <p class="mt-2 text-xs text-blue-700">Marked as solved.</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Total</dt>
            <dd class="mt-3 text-3xl font-bold text-slate-700">{{ $stats['total'] }}</dd>
            <p class="mt-2 text-xs text-slate-500">All submitted tickets.</p>
        </div>
    </dl>

    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-lg font-semibold text-slate-950">Recent tickets</h2>
                    <span class="rounded-lg bg-rose-600 px-2.5 py-1 text-sm font-bold text-white">{{ $stats['total'] }}</span>
                </div>
                <p class="mt-1 text-sm text-slate-500">Your latest requests and their current progress.</p>
            </div>
            <a href="{{ route('student.tickets.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                View all
            </a>
        </div>

        @if ($recentTickets->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm font-medium text-slate-700">You have not submitted any tickets yet.</p>
                <p class="mt-1 text-sm text-slate-500">Create your first ticket when you need help from support.</p>
                <a href="{{ route('student.tickets.create') }}" class="mt-5 inline-flex rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Create your first ticket
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Ticket</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3">Assigned staff</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($recentTickets as $ticket)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <a href="{{ route('student.tickets.show', $ticket) }}" class="font-semibold text-slate-950 hover:underline">
                                        #{{ $ticket->id }} - {{ Str::limit($ticket->subject, 48) }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $ticket->category->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $ticket->assignee?->name ?? 'Not assigned' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
