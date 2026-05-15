@extends('layouts.app')

@section('title', 'My dashboard')

@section('content')
    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My dashboard</h1>
            <p class="mt-1 text-slate-600">Welcome back, {{ auth()->user()->name }}.</p>
        </div>
        <a href="{{ route('student.tickets.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            New support ticket
        </a>
    </div>

    <dl class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Active</dt>
            <dd class="mt-1 text-3xl font-semibold text-slate-900">{{ $activeCount }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Open</dt>
            <dd class="mt-1 text-3xl font-semibold text-emerald-700">{{ $stats['open'] }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Pending</dt>
            <dd class="mt-1 text-3xl font-semibold text-amber-700">{{ $stats['pending'] }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Resolved</dt>
            <dd class="mt-1 text-3xl font-semibold text-blue-700">{{ $stats['resolved'] }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Total</dt>
            <dd class="mt-1 text-3xl font-semibold text-slate-700">{{ $stats['total'] }}</dd>
        </div>
    </dl>

    <section class="mt-10">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-900">Recent tickets</h2>
            <a href="{{ route('student.tickets.index') }}" class="text-sm font-medium text-slate-900 underline">View all</a>
        </div>

        @if ($recentTickets->isEmpty())
            <div class="mt-4 rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center">
                <p class="text-sm text-slate-600">You have not submitted any tickets yet.</p>
                <a href="{{ route('student.tickets.create') }}" class="mt-4 inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Create your first ticket
                </a>
            </div>
        @else
            <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Ticket</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Assigned staff</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentTickets as $ticket)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    <a href="{{ route('student.tickets.show', $ticket) }}" class="font-medium text-slate-900 hover:underline">
                                        #{{ $ticket->id }} - {{ Str::limit($ticket->subject, 48) }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $ticket->category->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $ticket->assignee?->name ?? 'Not assigned' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->status->badgeClass() }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
