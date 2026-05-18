@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . auth()->user()->name . ' 👋')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ── HERO BANNER ──────────────────────────────────────────────────────── --}}
    <div class="rounded-3xl p-7 mb-6 text-white relative overflow-hidden animate-fade-up"
         style="background:linear-gradient(135deg,#0f0c29 0%,#1e1b4b 35%,#312e81 70%,#4338ca 100%);box-shadow:0 8px 32px rgba(79,70,229,0.25);">
        <div class="absolute inset-0 opacity-[0.06]"
             style="background-image:radial-gradient(circle,white 1px,transparent 1px);background-size:28px 28px;"></div>
        <div class="absolute top-0 right-0 w-72 h-72 rounded-full opacity-20"
             style="background:radial-gradient(circle,#818cf8,transparent 65%);filter:blur(50px);transform:translate(25%,-35%);"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-indigo-400 text-[11px] font-bold uppercase tracking-[0.18em] mb-2">Staff Workspace</p>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }} 👋
                </h2>
                <p class="text-indigo-300 text-sm mt-1.5 font-medium">Here's what's happening in the support queue today.</p>
            </div>
            <a href="{{ route('admin.tickets.index') }}"
               class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-white/95 px-5 py-2.5 text-sm font-bold text-indigo-700 shadow-lg hover:bg-white hover:-translate-y-0.5 transition-all duration-150">
                View all tickets
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>

    {{-- ── STAT CARDS ───────────────────────────────────────────────────────── --}}
    @php
    $cards = [
        ['label' => 'Open',           'key' => 'open',           'color' => '#10b981', 'light' => '#d1fae5', 'filter' => ['status' => 'open']],
        ['label' => 'Pending',        'key' => 'pending',        'color' => '#f59e0b', 'light' => '#fef3c7', 'filter' => ['status' => 'pending']],
        ['label' => 'Unassigned',     'key' => 'unassigned',     'color' => '#ef4444', 'light' => '#fee2e2', 'filter' => ['assigned_to' => 'unassigned']],
        ['label' => 'Assigned to me', 'key' => 'assigned_to_me', 'color' => '#6366f1', 'light' => '#e0e7ff', 'filter' => ['assigned_to' => auth()->id()]],
        ['label' => 'Resolved',       'key' => 'resolved',       'color' => '#3b82f6', 'light' => '#dbeafe', 'filter' => ['status' => 'resolved']],
        ['label' => 'Closed',         'key' => 'closed',         'color' => '#94a3b8', 'light' => '#f1f5f9', 'filter' => ['status' => 'closed']],
    ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        @foreach ($cards as $i => $card)
            <a href="{{ route('admin.tickets.index', $card['filter']) }}"
               class="card group p-5 hover:-translate-y-1.5 transition-all duration-200 animate-fade-up"
               style="animation-delay:{{ $i * 0.04 }}s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110"
                         style="background-color:{{ $card['light'] }};">
                        <div class="w-3.5 h-3.5 rounded-full" style="background-color:{{ $card['color'] }};"></div>
                    </div>
                    <svg class="w-4 h-4 text-gray-200 group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <p class="text-3xl font-extrabold text-gray-900 tabular-nums">{{ $stats[$card['key']] }}</p>
                <p class="text-[11px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $card['label'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- ── BOTTOM GRID ──────────────────────────────────────────────────────── --}}
    <div class="grid gap-5 lg:grid-cols-3">

        {{-- Donut chart --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-gray-900">Queue Breakdown</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Ticket distribution by status</p>
                </div>
                @php $total = $stats['open'] + $stats['pending'] + $stats['resolved'] + $stats['closed']; @endphp
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 rounded-full px-3 py-1 border border-indigo-100">{{ $total }} total</span>
            </div>

            @if ($total > 0)
                <div class="flex justify-center mb-5">
                    <div class="relative w-36 h-36">
                        <canvas id="queueChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-extrabold text-gray-800">{{ $total }}</span>
                            <span class="text-xs text-gray-400">tickets</span>
                        </div>
                    </div>
                </div>
                <ul class="space-y-3">
                    @foreach ([
                        ['label' => 'Open',     'val' => $stats['open'],     'color' => '#10b981'],
                        ['label' => 'Pending',  'val' => $stats['pending'],  'color' => '#f59e0b'],
                        ['label' => 'Resolved', 'val' => $stats['resolved'], 'color' => '#3b82f6'],
                        ['label' => 'Closed',   'val' => $stats['closed'],   'color' => '#cbd5e1'],
                    ] as $row)
                        <li class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $row['color'] }};"></span>
                            <span class="flex-1 text-sm text-gray-600">{{ $row['label'] }}</span>
                            <span class="text-sm font-bold text-gray-800">{{ $row['val'] }}</span>
                            <span class="text-xs text-gray-400 w-8 text-right">{{ $total > 0 ? round(($row['val'] / $total) * 100) : 0 }}%</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex flex-col items-center justify-center h-48 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">No tickets yet</p>
                </div>
            @endif
        </div>

        {{-- Latest tickets --}}
        <div class="card lg:col-span-2 flex flex-col">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="font-bold text-gray-900">Latest Tickets</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Most recent activity across all students</p>
                </div>
                <a href="{{ route('admin.tickets.index') }}"
                   class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">View all →</a>
            </div>

            <ul class="flex-1 divide-y divide-gray-50">
                @forelse ($recentTickets as $ticket)
                    <li class="group px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-mono font-bold text-gray-400">#{{ $ticket->id }}</span>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">{{ $ticket->status->label() }}</span>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">{{ $ticket->priority->label() }}</span>
                                </div>
                                <a href="{{ route('admin.tickets.show', $ticket) }}"
                                   class="text-sm font-semibold text-gray-900 hover:text-indigo-600 truncate block transition">
                                    {{ Str::limit($ticket->subject, 55) }}
                                </a>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $ticket->user->name }} · {{ $ticket->category->name }}
                                    @if ($ticket->assignee)
                                        · <span class="text-indigo-500 font-medium">{{ $ticket->assignee->name }}</span>
                                    @else
                                        · <span class="text-red-400">Unassigned</span>
                                    @endif
                                </p>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0 mt-1">{{ $ticket->updated_at->diffForHumans() }}</span>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-16 text-center">
                        <p class="text-sm text-gray-400">No tickets yet.</p>
                    </li>
                @endforelse
            </ul>

            <div class="border-t border-gray-100 px-6 py-4">
                <a href="{{ route('admin.tickets.index') }}"
                   class="flex items-center justify-center gap-2 text-sm font-semibold text-gray-500 hover:text-indigo-600 transition">
                    View all tickets
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

@endsection

@push('head')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('queueChart');
    if (ctx && {{ $stats['open'] + $stats['pending'] + $stats['resolved'] + $stats['closed'] }} > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Pending', 'Resolved', 'Closed'],
                datasets: [{
                    data: [{{ $stats['open'] }}, {{ $stats['pending'] }}, {{ $stats['resolved'] }}, {{ $stats['closed'] }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#cbd5e1'],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
                }]
            },
            options: {
                cutout: '75%',
                plugins: { legend: { display: false } },
                responsive: true,
                maintainAspectRatio: true,
            }
        });
    }
});
</script>
@endpush
