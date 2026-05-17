@extends('layouts.app')

@section('title', 'My dashboard')
@section('page_title', 'Dashboard')

@section('content')

    {{-- Welcome banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-sky-500 p-6 shadow-lg mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-indigo-200 uppercase tracking-widest">Student workspace</p>
                <h1 class="mt-1 text-2xl font-bold text-white">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }} 👋
                </h1>
                <p class="mt-1 text-sm text-indigo-200">Track your support requests and stay updated on their progress.</p>
            </div>
            <a href="{{ route('student.tickets.create') }}"
               class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50 transition shrink-0">
                + New ticket
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-6">
        @foreach ([
            ['label' => 'Active',   'val' => $activeCount,       'color' => 'indigo', 'desc' => 'Open & pending'],
            ['label' => 'Open',     'val' => $stats['open'],     'color' => 'emerald','desc' => 'Awaiting staff'],
            ['label' => 'Pending',  'val' => $stats['pending'],  'color' => 'amber',  'desc' => 'Staff responded'],
            ['label' => 'Resolved', 'val' => $stats['resolved'], 'color' => 'blue',   'desc' => 'Marked solved'],
            ['label' => 'Total',    'val' => $stats['total'],    'color' => 'slate',  'desc' => 'All time'],
        ] as $card)
            @php
                $colors = [
                    'indigo'  => ['border' => 'border-indigo-200',  'bg' => 'bg-indigo-50',  'dt' => 'text-indigo-700',  'dd' => 'text-indigo-800',  'desc' => 'text-indigo-500'],
                    'emerald' => ['border' => 'border-emerald-200', 'bg' => 'bg-emerald-50', 'dt' => 'text-emerald-700', 'dd' => 'text-emerald-800', 'desc' => 'text-emerald-500'],
                    'amber'   => ['border' => 'border-amber-200',   'bg' => 'bg-amber-50',   'dt' => 'text-amber-700',   'dd' => 'text-amber-800',   'desc' => 'text-amber-500'],
                    'blue'    => ['border' => 'border-blue-200',    'bg' => 'bg-blue-50',    'dt' => 'text-blue-700',    'dd' => 'text-blue-800',    'desc' => 'text-blue-500'],
                    'slate'   => ['border' => 'border-slate-200',   'bg' => 'bg-white',      'dt' => 'text-slate-500',   'dd' => 'text-slate-700',   'desc' => 'text-slate-400'],
                ][$card['color']];
            @endphp
            <div class="rounded-2xl border {{ $colors['border'] }} {{ $colors['bg'] }} p-5 shadow-sm">
                <dt class="text-xs font-semibold uppercase tracking-wide {{ $colors['dt'] }}">{{ $card['label'] }}</dt>
                <dd class="mt-2 text-4xl font-extrabold {{ $colors['dd'] }}">{{ $card['val'] }}</dd>
                <p class="mt-1 text-xs {{ $colors['desc'] }}">{{ $card['desc'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Recent tickets --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <p class="font-semibold text-slate-900">Recent tickets</p>
                <p class="text-xs text-slate-400 mt-0.5">Your latest requests and their current progress.</p>
            </div>
            <a href="{{ route('student.tickets.index') }}"
               class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                View all
            </a>
        </div>

        @if ($recentTickets->isEmpty())
            <div class="py-16 text-center px-4">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 mb-4">
                    <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <p class="font-semibold text-slate-700">No tickets yet</p>
                <p class="mt-1 text-sm text-slate-400">Submit a ticket whenever you need IT support.</p>
                <a href="{{ route('student.tickets.create') }}"
                   class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Create your first ticket
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-left">Ticket</th>
                            <th class="px-5 py-3 text-left">Category</th>
                            <th class="px-5 py-3 text-left">Assigned to</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-left">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentTickets as $ticket)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('student.tickets.show', $ticket) }}"
                                       class="font-semibold text-slate-900 hover:text-indigo-600 hover:underline">
                                        #{{ $ticket->id }} — {{ Str::limit($ticket->subject, 44) }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $ticket->category->name }}</td>
                                <td class="px-5 py-3.5 text-slate-500">
                                    @if ($ticket->assignee)
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">
                                                {{ strtoupper(substr($ticket->assignee->name, 0, 1)) }}
                                            </span>
                                            {{ $ticket->assignee->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
