@extends('layouts.app')

@section('title', 'Reports')
@section('page_title', 'Reports')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- Date filter --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="from" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5">From</label>
                <input id="from" name="from" type="date" value="{{ optional($from)->format('Y-m-d') }}"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>
            <div>
                <label for="to" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5">To</label>
                <input id="to" name="to" type="date" value="{{ optional($to)->format('Y-m-d') }}"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                Apply filter
            </button>
            @if ($from || $to)
                <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Clear
                </a>
            @endif
        </form>
        @error('to')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Summary stat cards --}}
    @php
        $statusColors = [
            'open'     => ['bg' => 'bg-emerald-50',  'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'num' => 'text-emerald-800'],
            'pending'  => ['bg' => 'bg-amber-50',    'border' => 'border-amber-200',   'text' => 'text-amber-700',   'num' => 'text-amber-800'],
            'resolved' => ['bg' => 'bg-blue-50',     'border' => 'border-blue-200',    'text' => 'text-blue-700',    'num' => 'text-blue-800'],
            'closed'   => ['bg' => 'bg-slate-50',    'border' => 'border-slate-200',   'text' => 'text-slate-500',   'num' => 'text-slate-700'],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-6">
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm lg:col-span-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Total tickets</p>
            <p class="mt-2 text-4xl font-extrabold text-indigo-800">{{ $totalTickets }}</p>
            <p class="mt-1 text-xs text-indigo-500">{{ $from || $to ? 'In selected range' : 'All time' }}</p>
        </div>
        @foreach ($statusColors as $status => $colors)
            @php $count = $byStatus[$status] ?? 0; @endphp
            <div class="rounded-2xl border {{ $colors['border'] }} {{ $colors['bg'] }} p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide {{ $colors['text'] }}">
                    {{ \App\Enums\TicketStatus::tryFrom($status)?->label() ?? $status }}
                </p>
                <p class="mt-2 text-4xl font-extrabold {{ $colors['num'] }}">{{ $count }}</p>
                <p class="mt-1 text-xs {{ $colors['text'] }}">
                    {{ $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0 }}% of total
                </p>
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid gap-6 lg:grid-cols-2 mb-6">
        {{-- Doughnut: by status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Tickets by status</h2>
            <p class="text-xs text-slate-400 mb-5">Distribution of all tickets across statuses.</p>
            @if ($byStatus->isNotEmpty())
                <div class="flex items-center justify-center">
                    <div class="w-56 h-56">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                <ul class="mt-5 space-y-2">
                    @foreach ($byStatus as $status => $count)
                        @php $label = \App\Enums\TicketStatus::tryFrom($status)?->label() ?? $status; @endphp
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">{{ $label }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-24 h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-2 rounded-full bg-indigo-500"
                                         style="width: {{ $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0 }}%"></div>
                                </div>
                                <span class="font-semibold text-slate-900 w-6 text-right">{{ $count }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex items-center justify-center h-40 text-sm text-slate-400">No data for this range.</div>
            @endif
        </div>

        {{-- Bar: by category --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Tickets by category</h2>
            <p class="text-xs text-slate-400 mb-5">Which issue types are submitted most.</p>
            @if ($byCategory->isNotEmpty())
                <div class="h-56">
                    <canvas id="categoryChart"></canvas>
                </div>
                <ul class="mt-5 space-y-2">
                    @foreach ($byCategory as $name => $count)
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">{{ $name }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-24 h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-2 rounded-full bg-violet-500"
                                         style="width: {{ $byCategory->max() > 0 ? round(($count / $byCategory->max()) * 100) : 0 }}%"></div>
                                </div>
                                <span class="font-semibold text-slate-900 w-6 text-right">{{ $count }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex items-center justify-center h-40 text-sm text-slate-400">No data for this range.</div>
            @endif
        </div>
    </div>

    {{-- Recently resolved --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Recently resolved tickets</h2>
                <p class="text-xs text-slate-400 mt-0.5">Last 20 tickets marked as resolved{{ $from || $to ? ' in selected range' : '' }}.</p>
            </div>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">{{ $recentResolved->count() }}</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Ticket</th>
                    <th class="px-5 py-3 text-left">Student</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-left">Resolved</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($recentResolved as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.tickets.show', $t) }}"
                               class="font-medium text-indigo-600 hover:underline">#{{ $t->id }}</a>
                            <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($t->subject, 48) }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $t->user->name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $t->category->name }}</td>
                        <td class="px-5 py-3 text-slate-400 text-xs">{{ $t->updated_at->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-400">No resolved tickets in this range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

@push('head')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Status doughnut chart
    @if ($byStatus->isNotEmpty())
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($byStatus->keys()->map(fn($s) => \App\Enums\TicketStatus::tryFrom($s)?->label() ?? $s)),
                datasets: [{
                    data: @json($byStatus->values()),
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#94a3b8'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                cutout: '68%',
                plugins: { legend: { display: false } },
                responsive: true,
                maintainAspectRatio: true,
            }
        });
    }
    @endif

    // Category bar chart
    @if ($byCategory->isNotEmpty())
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: @json($byCategory->keys()),
                datasets: [{
                    label: 'Tickets',
                    data: @json($byCategory->values()),
                    backgroundColor: '#818cf8',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 } },
                        grid: { color: '#f1f5f9' },
                    },
                    x: {
                        ticks: { font: { size: 11 } },
                        grid: { display: false },
                    }
                }
            }
        });
    }
    @endif

});
</script>
@endpush
