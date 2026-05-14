@extends('layouts.app')

@section('title', 'Staff dashboard')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Support dashboard</h1>
    <p class="mt-1 text-slate-600">Overview of the help desk queue.</p>

    <dl class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Open</dt>
            <dd class="mt-1 text-3xl font-semibold text-emerald-700">{{ $stats['open'] }}</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Pending</dt>
            <dd class="mt-1 text-3xl font-semibold text-amber-700">{{ $stats['pending'] }}</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Unassigned (open/pending)</dt>
            <dd class="mt-1 text-3xl font-semibold text-slate-900">{{ $stats['unassigned'] }}</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Resolved</dt>
            <dd class="mt-1 text-3xl font-semibold text-blue-700">{{ $stats['resolved'] }}</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Closed</dt>
            <dd class="mt-1 text-3xl font-semibold text-slate-600">{{ $stats['closed'] }}</dd>
        </div>
    </dl>

    <section class="mt-12">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-900">Latest tickets</h2>
            <a href="{{ route('admin.tickets.index') }}" class="text-sm font-medium text-slate-900 underline">View all</a>
        </div>
        <ul class="mt-4 divide-y divide-slate-200 rounded-xl border border-slate-200 bg-white shadow-sm">
            @foreach ($recentTickets as $t)
                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 hover:bg-slate-50/80">
                    <div>
                        <a href="{{ route('admin.tickets.show', $t) }}" class="font-medium text-slate-900 hover:underline">
                            #{{ $t->id }} — {{ Str::limit($t->subject, 56) }}
                        </a>
                        <p class="text-xs text-slate-500">{{ $t->user->name }} · {{ $t->category->name }}</p>
                    </div>
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $t->status->badgeClass() }}">
                        {{ $t->status->label() }}
                    </span>
                </li>
            @endforeach
        </ul>
    </section>
@endsection
