@extends('layouts.app')

@section('title', 'Staff dashboard')
@section('page_title', 'Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- Welcome banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 p-6 shadow-lg mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-indigo-200 uppercase tracking-widest">Staff workspace</p>
                <h1 class="mt-1 text-2xl font-bold text-white">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }} 👋
                </h1>
                <p class="mt-1 text-sm text-indigo-200">Here's what's happening in the support queue today.</p>
            </div>
            <a href="{{ route('admin.tickets.index') }}"
               class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50 transition shrink-0">
                Manage tickets →
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 mb-6">
        @foreach ([
            ['label' => 'Open',          'key' => 'open',          'color' => 'emerald', 'filter' => ['status' => 'open'],                        'desc' => 'New requests'],
            ['label' => 'Pending',       'key' => 'pending',       'color' => 'amber',   'filter' => ['status' => 'pending'],                     'desc' => 'In progress'],
            ['label' => 'Unassigned',    'key' => 'unassigned',    'color' => 'rose',    'filter' => ['assigned_to' => 'unassigned'],             'desc' => 'Needs owner'],
            ['label' => 'Assigned to me','key' => 'assigned_to_me','color' => 'indigo',  'filter' => ['assigned_to' => auth()->id()],             'desc' => 'Your queue'],
            ['label' => 'Resolved',      'key' => 'resolved',      'color' => 'blue',    'filter' => ['status' => 'resolved'],                    'desc' => 'Ready to close'],
            ['label' => 'Closed',        'key' => 'closed',        'color' => 'slate',   'filter' => ['status' => 'closed'],                      'desc' => 'Completed'],
        ] as $card)
            @php
                $colors = [
                    'emerald' => ['border' => 'border-emerald-200', 'bg' => 'bg-emerald-50',  'dt' => 'text-emerald-700', 'dd' => 'text-emerald-800', 'desc' => 'text-emerald-600'],
                    'amber'   => ['border' => 'border-amber-200',   'bg' => 'bg-amber-50',    'dt' => 'text-amber-700',   'dd' => 'text-amber-800',   'desc' => 'text-amber-600'],
                    'rose'    => ['border' => 'border-rose-200',    'bg' => 'bg-rose-50',     'dt' => 'text-rose-700',    'dd' => 'text-rose-800',    'desc' => 'text-rose-600'],
                    'indigo'  => ['border' => 'border-indigo-200',  'bg' => 'bg-indigo-50',   'dt' => 'text-indigo-700',  'dd' => 'text-indigo-800',  'desc' => 'text-indigo-600'],
                    'blue'    => ['border' => 'border-blue-200',    'bg' => 'bg-blue-50',     'dt' => 'text-blue-700',    'dd' => 'text-blue-800',    'desc' => 'text-blue-600'],
                    'slate'   => ['border' => 'border-slate-200',   'bg' => 'bg-white',       'dt' => 'text-slate-500',   'dd' => 'text-slate-700',   'desc' => 'text-slate-400'],
                ][$card['color']];
            @endphp
            <a href="{{ route('admin.tickets.index', $card['filter']) }}"
               class="rounded-2xl border {{ $colors['border'] }} {{ $colors['bg'] }} p-5 shadow-sm hover:shadow-md transition group">
                <dt class="text-xs font-semibold uppercase tracking-wide {{ $colors['dt'] }}">{{ $card['label'] }}</dt>
                <dd class="mt-2 text-4xl font-extrabold {{ $colors['dd'] }} group-hover:scale-105 transition-transform origin-left">
                    {{ $stats[$card['key']] }}
                </dd>
                <p class="mt-1 text-xs {{ $colors['desc'] }}">{{ $card['desc'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3 mb-6">
        {{-- Mini donut chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold text-slate-900 mb-1">Queue breakdown</p>
            <p class="text-xs text-slate-400 mb-4">Active ticket distribution.</p>
            @php $total = $stats['open'] + $stats['pending'] + $stats['resolved'] + $stats['closed']; @endphp
            @if ($total > 0)
                <div class="flex justify-center">
                    <div class="w-36 h-36"><canvas id="queueChart"></canvas></div>
                </div>
                <ul class="mt-4 space-y-2">
                    @foreach ([
                        ['label' => 'Open',     'val' => $stats['open'],     'color' => 'bg-emerald-500'],
                        ['label' => 'Pending',  'val' => $stats['pending'],  'color' => 'bg-amber-500'],
                        ['label' => 'Resolved', 'val' => $stats['resolved'], 'color' => 'bg-blue-500'],
                        ['label' => 'Closed',   'val' => $stats['closed'],   'color' => 'bg-slate-300'],
                    ] as $row)
                        <li class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-slate-600">
                                <span class="w-2 h-2 rounded-full {{ $row['color'] }}"></span>
                                {{ $row['label'] }}
                            </span>
                            <span class="font-semibold text-slate-800">{{ $row['val'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex items-center justify-center h-36 text-sm text-slate-400">No tickets yet.</div>
            @endif
        </div>

        {{-- Latest tickets --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <p class="font-semibold text-slate-900">Latest tickets</p>
                    <p class="text-xs text-slate-400 mt-0.5">Most recent activity across all students.</p>
                </div>
                <a href="{{ route('admin.tickets.index') }}"
                   class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    View all
                </a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($recentTickets as $ticket)
                    <li class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-slate-50 transition">
                        <div class="min-w-0">
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="text-sm font-semibold text-slate-900 hover:text-indigo-600 hover:underline truncate block">
                                #{{ $ticket->id }} — {{ Str::limit($ticket->subject, 50) }}
                            </a>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $ticket->user->name }} · {{ $ticket->category->name }} · {{ $ticket->assignee?->name ?? 'Unassigned' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                                {{ $ticket->status->label() }}
                            </span>
                            <span class="text-xs text-slate-400 hidden sm:block">{{ $ticket->updated_at->diffForHumans() }}</span>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center text-sm text-slate-400">No tickets yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

@endsection

@push('head')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('queueChart');
    if (ctx && {{ $total }} > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Pending', 'Resolved', 'Closed'],
                datasets: [{
                    data: [{{ $stats['open'] }}, {{ $stats['pending'] }}, {{ $stats['resolved'] }}, {{ $stats['closed'] }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#cbd5e1'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 4,
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } },
                responsive: true,
                maintainAspectRatio: true,
            }
        });
    }
});
</script>
@endpush
