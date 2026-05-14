@extends('layouts.app')

@section('title', 'My dashboard')

@section('content')
    <div class="flex flex-col gap-8 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My dashboard</h1>
            <p class="mt-1 text-slate-600">Welcome back, {{ auth()->user()->name }}.</p>
        </div>
        <a href="{{ route('student.tickets.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            New support ticket
        </a>
    </div>

    <dl class="mt-10 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Open or pending tickets</dt>
            <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ $openCount }}</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <dt class="text-sm font-medium text-slate-500">Quick link</dt>
            <dd class="mt-2">
                <a href="{{ route('student.tickets.index') }}" class="text-sm font-semibold text-slate-900 underline">View all my tickets</a>
            </dd>
        </div>
    </dl>

    <section class="mt-12">
        <h2 class="text-lg font-semibold text-slate-900">Recent tickets</h2>
        @if ($recentTickets->isEmpty())
            <p class="mt-4 text-slate-600">You have not submitted any tickets yet.</p>
        @else
            <ul class="mt-4 divide-y divide-slate-200 rounded-xl border border-slate-200 bg-white shadow-sm">
                @foreach ($recentTickets as $t)
                    <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                        <div>
                            <a href="{{ route('student.tickets.show', $t) }}" class="font-medium text-slate-900 hover:underline">
                                #{{ $t->id }} — {{ $t->subject }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $t->category->name }} · {{ $t->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $t->status->badgeClass() }}">
                            {{ $t->status->label() }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
