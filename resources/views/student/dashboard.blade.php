@extends('layouts.app')

@section('title', 'My Dashboard')
@section('page_title', 'My Dashboard')
@section('page_subtitle', 'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . auth()->user()->name . ' 👋')

@section('content')

    {{-- ── HERO BANNER ──────────────────────────────────────────────────────── --}}
    <div class="rounded-3xl p-7 mb-6 text-white relative overflow-hidden animate-fade-in-up"
         style="background: linear-gradient(135deg, #0c1a2e 0%, #0f2744 35%, #1e3a5f 65%, #0ea5e9 100%); box-shadow: 0 8px 32px rgba(14,165,233,0.25);">
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-20"
             style="background: radial-gradient(circle, #38bdf8, transparent 65%); filter: blur(40px); transform: translate(20%, -30%);"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sky-400 text-xs font-bold uppercase tracking-[0.15em] mb-2">Student Workspace</p>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }} 👋
                </h2>
                <p class="text-sky-200 text-sm mt-1.5">Track your support requests and stay updated on their progress.</p>
            </div>
            <a href="{{ route('student.tickets.create') }}"
               class="shrink-0 inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-2.5 text-sm font-bold text-sky-700 shadow-lg hover:bg-sky-50 hover:-translate-y-0.5 transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                New Ticket
            </a>
        </div>
    </div>

    {{-- ── STAT CARDS ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        @php
        $cards = [
            ['label' => 'Active',   'val' => $activeCount,       'color' => '#6366f1', 'light' => '#e0e7ff', 'desc' => 'Open & pending'],
            ['label' => 'Open',     'val' => $stats['open'],     'color' => '#10b981', 'light' => '#d1fae5', 'desc' => 'Awaiting staff'],
            ['label' => 'Pending',  'val' => $stats['pending'],  'color' => '#f59e0b', 'light' => '#fef3c7', 'desc' => 'Staff responded'],
            ['label' => 'Resolved', 'val' => $stats['resolved'], 'color' => '#3b82f6', 'light' => '#dbeafe', 'desc' => 'Marked solved'],
            ['label' => 'Total',    'val' => $stats['total'],    'color' => '#94a3b8', 'light' => '#f1f5f9', 'desc' => 'All time'],
        ];
        @endphp

        @foreach ($cards as $card)
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4"
                     style="background-color: {{ $card['light'] }};">
                    <div class="w-4 h-4 rounded-full" style="background-color: {{ $card['color'] }};"></div>
                </div>
                <p class="text-3xl font-extrabold text-gray-900 tabular-nums">{{ $card['val'] }}</p>
                <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">{{ $card['label'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $card['desc'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ── QUICK ACTIONS ────────────────────────────────────────────────────── --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('student.tickets.create') }}"
           class="group flex items-center gap-4 rounded-2xl p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
           style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="font-bold text-white">New Ticket</p>
                <p class="text-indigo-200 text-xs mt-0.5">Submit a support request</p>
            </div>
            <svg class="w-5 h-5 text-white/50 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="{{ route('student.tickets.index') }}"
           class="group flex items-center gap-4 rounded-2xl bg-white border border-gray-200 p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">My Tickets</p>
                <p class="text-gray-400 text-xs mt-0.5">View all your requests</p>
            </div>
            <svg class="w-5 h-5 text-gray-300 ml-auto group-hover:text-indigo-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="{{ route('knowledge-base.index') }}"
           class="group flex items-center gap-4 rounded-2xl bg-white border border-gray-200 p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <p class="font-bold text-gray-900">Knowledge Base</p>
                <p class="text-gray-400 text-xs mt-0.5">Browse help articles</p>
            </div>
            <svg class="w-5 h-5 text-gray-300 ml-auto group-hover:text-indigo-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- ── RECENT TICKETS ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-900">Recent Tickets</h3>
                <p class="text-xs text-gray-400 mt-0.5">Your latest support requests</p>
            </div>
            <a href="{{ route('student.tickets.index') }}"
               class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">View all →</a>
        </div>

        @if ($recentTickets->isEmpty())
            <div class="py-20 text-center px-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 mb-4">
                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <p class="font-bold text-gray-700 text-lg">No tickets yet</p>
                <p class="mt-1 text-sm text-gray-400">Submit a ticket whenever you need IT support.</p>
                <a href="{{ route('student.tickets.create') }}"
                   class="mt-5 inline-flex rounded-xl px-6 py-3 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5"
                   style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    Create your first ticket
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($recentTickets as $ticket)
                    <a href="{{ route('student.tickets.show', $ticket) }}"
                       class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition group">
                        @php
                            $dot = match($ticket->status->value) {
                                'open'     => '#10b981',
                                'pending'  => '#f59e0b',
                                'resolved' => '#3b82f6',
                                default    => '#94a3b8',
                            };
                        @endphp
                        <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $dot }};"></div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition">
                                {{ Str::limit($ticket->subject, 55) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                #{{ $ticket->id }} · {{ $ticket->category->name }}
                                @if ($ticket->assignee)
                                    · <span class="text-indigo-500 font-medium">{{ $ticket->assignee->name }}</span>
                                @else
                                    · <span class="text-gray-400">Not assigned</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="hidden sm:inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">{{ $ticket->priority->label() }}</span>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">{{ $ticket->status->label() }}</span>
                            <span class="hidden md:block text-xs text-gray-400">{{ $ticket->updated_at->diffForHumans() }}</span>
                        </div>

                        <svg class="w-4 h-4 text-gray-300 shrink-0 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endforeach
            </div>
            <div class="border-t border-gray-100 px-6 py-4">
                <a href="{{ route('student.tickets.index') }}"
                   class="flex items-center justify-center gap-2 text-sm font-semibold text-gray-500 hover:text-indigo-600 transition">
                    View all tickets
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif
    </div>

@endsection
