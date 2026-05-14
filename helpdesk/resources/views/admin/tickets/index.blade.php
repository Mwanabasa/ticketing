@extends('layouts.app')

@section('title', 'All tickets')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">All tickets</h1>
    <p class="mt-1 text-slate-600">Search and filter the queue.</p>

    <form method="GET" action="{{ route('admin.tickets.index') }}" class="mt-8 grid gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label for="q" class="block text-xs font-medium uppercase text-slate-500">Search</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Subject, body, or ID"
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="status" class="block text-xs font-medium uppercase text-slate-500">Status</label>
            <select id="status" name="status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any</option>
                @foreach (\App\Enums\TicketStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="category_id" class="block text-xs font-medium uppercase text-slate-500">Category</label>
            <select id="category_id" name="category_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected((string) request('category_id') === (string) $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="assigned_to" class="block text-xs font-medium uppercase text-slate-500">Assignee</label>
            <select id="assigned_to" name="assigned_to" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any</option>
                <option value="unassigned" @selected(request('assigned_to') === 'unassigned')>Unassigned</option>
                @foreach ($staff as $member)
                    <option value="{{ $member->id }}" @selected((string) request('assigned_to') === (string) $member->id)>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-4">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Apply filters</button>
            <a href="{{ route('admin.tickets.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Ticket</th>
                    <th class="px-4 py-3">Student</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Assignee</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tickets as $ticket)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-medium text-slate-900 hover:underline">
                                #{{ $ticket->id }}
                            </a>
                            <div class="text-xs text-slate-500">{{ Str::limit($ticket->subject, 40) }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $ticket->user->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $ticket->category->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $ticket->assignee?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->status->badgeClass() }}">
                                {{ $ticket->status->label() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">No tickets match your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $tickets->links() }}</div>
@endsection
