@extends('layouts.app')

@section('title', 'All tickets')
@section('page_title', 'Reactive tickets')

@section('content')
    @php
        $statusTabs = [
            ['label' => 'All', 'value' => null],
            ['label' => 'Open', 'value' => 'open'],
            ['label' => 'Pending', 'value' => 'pending'],
            ['label' => 'Resolved', 'value' => 'resolved'],
            ['label' => 'Closed', 'value' => 'closed'],
        ];
    @endphp

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="p-6 sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Ticket management</p>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-bold tracking-tight text-slate-950">Tickets</h1>
                <span class="rounded-lg bg-rose-600 px-2.5 py-1 text-sm font-bold text-white">{{ $tickets->total() }}</span>
            </div>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                Search the queue, filter by status or owner, then update assignment and status without leaving the list.
            </p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        @foreach ($statusTabs as $tab)
            @php
                $isActive = $tab['value'] === null
                    ? ! request()->filled('status')
                    : request('status') === $tab['value'];
                $url = $tab['value'] === null
                    ? route('admin.tickets.index', request()->except('status', 'page'))
                    : route('admin.tickets.index', array_merge(request()->except('page'), ['status' => $tab['value']]));
            @endphp
            <a href="{{ $url }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm {{ $isActive ? 'bg-white text-emerald-700 ring-1 ring-emerald-200' : 'bg-white text-slate-700 ring-1 ring-slate-200' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.tickets.index') }}" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="q" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
                <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Subject, body, or ID"
                    class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select id="status" name="status" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <option value="">Any status</option>
                    @foreach (\App\Enums\TicketStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="category_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Category</label>
                <select id="category_id" name="category_id" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <option value="">Any category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="assigned_to" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Assignee</label>
                <select id="assigned_to" name="assigned_to" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <option value="">Any assignee</option>
                    <option value="unassigned" @selected(request('assigned_to') === 'unassigned')>Unassigned</option>
                    @foreach ($staff as $member)
                        <option value="{{ $member->id }}" @selected((string) request('assigned_to') === (string) $member->id)>{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm">Apply filters</button>
            <a href="{{ route('admin.tickets.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700">Reset</a>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Ticket</th>
                        <th class="px-5 py-3">Student</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Assignment and status</th>
                        <th class="px-5 py-3">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td class="px-5 py-4 align-top">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-semibold text-slate-950">
                                    #{{ $ticket->id }} - {{ Str::limit($ticket->subject, 56) }}
                                </a>
                                <div class="mt-2">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <div class="font-medium text-slate-800">{{ $ticket->user->name }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $ticket->user->email }}</div>
                            </td>
                            <td class="px-5 py-4 align-top text-slate-600">{{ $ticket->category->name }}</td>
                            <td class="px-5 py-4 align-top">
                                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="grid gap-2 lg:grid-cols-[minmax(9rem,1fr)_minmax(11rem,1fr)_auto]">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only" for="status-{{ $ticket->id }}">Status</label>
                                    <select id="status-{{ $ticket->id }}" name="status" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                                        @foreach (\App\Enums\TicketStatus::cases() as $status)
                                            <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>

                                    <label class="sr-only" for="assigned-to-{{ $ticket->id }}">Assignee</label>
                                    <select id="assigned-to-{{ $ticket->id }}" name="assigned_to" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                                        <option value="">Unassigned</option>
                                        @foreach ($staff as $member)
                                            <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }}</option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm">
                                        Save
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-4 align-top text-slate-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="font-medium text-slate-700">No tickets match your filters.</p>
                                <p class="mt-1 text-sm text-slate-500">Try clearing filters or searching a different term.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $tickets->links() }}</div>
@endsection
