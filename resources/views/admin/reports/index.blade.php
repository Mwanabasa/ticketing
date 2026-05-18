@extends('layouts.app')
@section('title', 'Reports')
@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Ticket statistics and staff performance overview')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- Date filter --}}
    <div class="card p-5 mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="from" class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">From date</label>
                <input id="from" name="from" type="date" value="{{ optional($from)->format('Y-m-d') }}" class="input py-2.5 text-sm w-auto">
            </div>
            <div>
                <label for="to" class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">To date</label>
                <input id="to" name="to" type="date" value="{{ optional($to)->format('Y-m-d') }}" class="input py-2.5 text-sm w-auto">
            </div>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
            @if ($from || $to)
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
        @error('to') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
        @if ($from || $to)
            <p class="mt-3 text-xs text-indigo-600 font-semibold">
                📅 Showing data {{ $from ? 'from '.$from->format('M j, Y') : '' }}{{ $from && $to ? ' to ' : '' }}{{ $to ? $to->format('M j, Y') : '' }}
            </p>
        @endif
    </div>

    {{-- Stat cards --}}
    @php
        $statCards = [
            ['label' => 'Total Tickets', 'val' => $totalTickets,              'pct' => null,  'gradient' => 'linear-gradient(135deg, #4f46e5, #7c3aed)', 'icon' => '🎫'],
            ['label' => 'Open',          'val' => $byStatus['open'] ?? 0,     'pct' => true,  'gradient' => 'linear-gradient(135deg, #10b981, #059669)',   'icon' => '📬'],
            ['label' => 'Pending',       'val' => $byStatus['pending'] ?? 0,  'pct' => true,  'gradient' => 'linear-gradient(135deg, #f59e0b, #d97706)',   'icon' => '⏳'],
            ['label' => 'Resolved',      'val' => $byStatus['resolved'] ?? 0, 'pct' => true,  'gradient' => 'linear-gradient(135deg, #3b82f6, #2563eb)',   'icon' => '✅'],
            ['label' => 'Closed',        'val' => $byStatus['closed'] ?? 0,   'pct' => true,  'gradient' => 'linear-gradient(135deg, #94a3b8, #64748b)',   'icon' => '🔒'],
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        @foreach ($statCards as $card)
            <div class="rounded-2xl p-5 text-white shadow-lg relative overflow-hidden" style="background: {{ $card['gradient'] }};">
                <div class="absolute top-3 right-3 text-2xl opacity-20">{{ $card['icon'] }}</div>
                <p class="text-3xl font-extrabold tabular-nums">{{ $card['val'] }}</p>
                <p class="text-xs font-semibold uppercase tracking-widest mt-1 opacity-80">{{ $card['label'] }}</p>
                @if ($card['pct'] && $totalTickets > 0)
                    <p class="text-xs mt-1 opacity-70">{{ round(($card['val'] / $totalTickets) * 100) }}% of total</p>
                @else
                    <p class="text-xs mt-1 opacity-70">{{ $from || $to ? 'In range' : 'All time' }}</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid gap-5 lg:grid-cols-2 mb-6">

        {{-- Status donut --}}
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Tickets by Status</h3>
                    <p class="text-xs text-gray-400">Distribution across all statuses</p>
                </div>
            </div>
            @if ($byStatus->isNotEmpty())
                <div class="flex justify-center mb-5">
                    <div class="w-48 h-48"><canvas id="statusChart"></canvas></div>
                </div>
                <ul class="space-y-2.5">
                    @foreach ($byStatus as $status => $count)
                        @php
                            $label = \App\Enums\TicketStatus::tryFrom($status)?->label() ?? $status;
                            $pct = $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0;
                            $colors = ['open' => '#10b981', 'pending' => '#f59e0b', 'resolved' => '#3b82f6', 'closed' => '#94a3b8'];
                            $color = $colors[$status] ?? '#94a3b8';
                        @endphp
                        <li class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $color }};"></span>
                            <span class="flex-1 text-sm text-gray-600">{{ $label }}</span>
                            <div class="w-24 h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-2 rounded-full" style="width: {{ $pct }}%; background-color: {{ $color }};"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-800 w-6 text-right">{{ $count }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex items-center justify-center h-48 text-sm text-gray-400">No data for this range.</div>
            @endif
        </div>

        {{-- Category bar --}}
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Tickets by Category</h3>
                    <p class="text-xs text-gray-400">Most common issue types</p>
                </div>
            </div>
            @if ($byCategory->isNotEmpty())
                <div class="h-48 mb-5"><canvas id="categoryChart"></canvas></div>
                <ul class="space-y-2.5">
                    @foreach ($byCategory as $name => $count)
                        @php $pct = $byCategory->max() > 0 ? round(($count / $byCategory->max()) * 100) : 0; @endphp
                        <li class="flex items-center gap-3">
                            <span class="flex-1 text-sm text-gray-600 truncate">{{ $name }}</span>
                            <div class="w-24 h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $pct }}%;"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-800 w-6 text-right">{{ $count }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex items-center justify-center h-48 text-sm text-gray-400">No data for this range.</div>
            @endif
        </div>
    </div>

    {{-- Staff performance --}}
    <div class="card overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #f8faff, #f3f0ff);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Staff Performance</h3>
                    <p class="text-xs text-gray-400">Tickets resolved and satisfaction ratings per staff member</p>
                </div>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Staff member</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Assigned</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Resolved</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Closed</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Avg rating</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($staffPerformance as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                     style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $member->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-gray-700">{{ $member->total_assigned }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ $member->resolved_count }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">{{ $member->closed_count }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($member->avg_rating)
                                <div class="flex items-center gap-1.5">
                                    <div class="flex text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="text-sm {{ $i <= round($member->avg_rating) ? 'text-amber-400' : 'text-gray-200' }}">★</span>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($member->avg_rating, 1) }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">No ratings yet</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">No staff members found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recently resolved --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-900">Recently Resolved</h3>
                <p class="text-xs text-gray-400 mt-0.5">Last 20 tickets marked as resolved{{ $from || $to ? ' in selected range' : '' }}</p>
            </div>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ $recentResolved->count() }}</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Ticket</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Student</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">Resolved</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($recentResolved as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('admin.tickets.show', $t) }}"
                               class="font-semibold text-indigo-600 hover:text-indigo-700 hover:underline">#{{ $t->id }}</a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($t->subject, 50) }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $t->user->name }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-600">{{ $t->category->name }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-400">{{ $t->updated_at->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-gray-400">No resolved tickets in this range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

@push('head')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if ($byStatus->isNotEmpty())
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($byStatus->keys()->map(fn($s) => \App\Enums\TicketStatus::tryFrom($s)?->label() ?? $s)),
                datasets: [{
                    data: @json($byStatus->values()),
                    backgroundColor: ['#10b981','#f59e0b','#3b82f6','#94a3b8'],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
                }]
            },
            options: {
                cutout: '72%',
                plugins: { legend: { display: false } },
                responsive: true,
                maintainAspectRatio: true,
            }
        });
    }
    @endif

    @if ($byCategory->isNotEmpty())
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: @json($byCategory->keys()),
                datasets: [{
                    data: @json($byCategory->values()),
                    backgroundColor: 'rgba(99,102,241,0.8)',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f3f4f6' } },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                }
            }
        });
    }
    @endif
});
</script>
@endpush
