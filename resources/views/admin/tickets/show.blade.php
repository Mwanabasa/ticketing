@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)
@section('page_title', 'Ticket #'.$ticket->id)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">← All tickets</a>

        {{-- Quick action buttons --}}
        <div class="flex flex-wrap gap-2">
            @if ($ticket->status !== \App\Enums\TicketStatus::Resolved)
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="resolved">
                    <input type="hidden" name="priority" value="{{ $ticket->priority->value }}">
                    <input type="hidden" name="assigned_to" value="{{ $ticket->assigned_to ?? '' }}">
                    <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                        ✓ Mark resolved
                    </button>
                </form>
            @endif
            @if ($ticket->status !== \App\Enums\TicketStatus::Closed)
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}"
                      onsubmit="return confirm('Close this ticket? The student will no longer be able to reply.')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="closed">
                    <input type="hidden" name="priority" value="{{ $ticket->priority->value }}">
                    <input type="hidden" name="assigned_to" value="{{ $ticket->assigned_to ?? '' }}">
                    <button type="submit" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Close ticket
                    </button>
                </form>
            @endif
            @if ($ticket->assigned_to !== auth()->id())
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $ticket->status->value }}">
                    <input type="hidden" name="priority" value="{{ $ticket->priority->value }}">
                    <input type="hidden" name="assigned_to" value="{{ auth()->id() }}">
                    <button type="submit" class="rounded-xl border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                        Assign to me
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Header --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $ticket->subject }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $ticket->user->name }} ({{ $ticket->user->email }}) · {{ $ticket->category->name }}
                    · Opened {{ $ticket->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="flex gap-2 shrink-0">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">
                    {{ $ticket->priority->label() }}
                </span>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                    {{ $ticket->status->label() }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left: description + thread + reply --}}
        <div class="space-y-5 lg:col-span-2">
            @if ($ticket->attachment_path)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Attachment</p>
                    <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" rel="noopener">
                        <img src="{{ asset('storage/'.$ticket->attachment_path) }}" alt="Attachment"
                             class="max-h-64 max-w-full rounded-xl border object-contain">
                    </a>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Original description</p>
                <div class="whitespace-pre-wrap text-sm text-slate-800 leading-relaxed">{{ $ticket->description }}</div>
            </div>

            {{-- Thread --}}
            @if ($ticket->replies->isNotEmpty())
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Conversation ({{ $ticket->replies->count() }})</p>
                    @foreach ($ticket->replies as $reply)
                        <div class="rounded-2xl border p-4 shadow-sm
                            {{ $reply->user->isStaff() ? 'border-indigo-100 bg-indigo-50' : 'border-slate-200 bg-white' }}">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $reply->user->isStaff() ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $reply->user->name }}</span>
                                    @if ($reply->user->isStaff())
                                        <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">Staff</span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400">{{ $reply->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            <p class="whitespace-pre-wrap text-sm text-slate-700 leading-relaxed">{{ $reply->body }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Reply form --}}
            @if ($ticket->status !== \App\Enums\TicketStatus::Closed)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-900 mb-3">Reply to student</p>
                    <form method="POST" action="{{ route('admin.tickets.replies.store', $ticket) }}">
                        @csrf
                        <textarea name="body" rows="4" required placeholder="Type your reply…"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('body') }}</textarea>
                        <div class="mt-3 flex gap-2">
                            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                                Send reply
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center text-sm text-slate-400">
                    This ticket is closed. No further replies can be added.
                </div>
            @endif
        </div>

        {{-- Right: manage sidebar --}}
        <aside class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-4">Manage ticket</p>
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label for="status" class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                        <select id="status" name="status"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            @foreach (\App\Enums\TicketStatus::cases() as $s)
                                <option value="{{ $s->value }}" @selected(old('status', $ticket->status->value) === $s->value)>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-xs font-medium text-slate-500 mb-1">Priority</label>
                        <select id="priority" name="priority"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            @foreach (\App\Enums\TicketPriority::cases() as $p)
                                <option value="{{ $p->value }}" @selected(old('priority', $ticket->priority->value) === $p->value)>{{ $p->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="assigned_to" class="block text-xs font-medium text-slate-500 mb-1">Assigned to</label>
                        <select id="assigned_to" name="assigned_to"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            <option value="">Unassigned</option>
                            @foreach ($staff as $member)
                                <option value="{{ $member->id }}" @selected(old('assigned_to', $ticket->assigned_to) == $member->id)>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Save changes
                    </button>
                </form>
            </div>

            {{-- Ticket info --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3 text-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ticket info</p>
                <div class="flex justify-between">
                    <span class="text-slate-500">Student</span>
                    <span class="font-medium text-slate-800">{{ $ticket->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Category</span>
                    <span class="font-medium text-slate-800">{{ $ticket->category->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Assignee</span>
                    <span class="font-medium text-slate-800">{{ $ticket->assignee?->name ?? 'Unassigned' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Replies</span>
                    <span class="font-medium text-slate-800">{{ $ticket->replies->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Opened</span>
                    <span class="font-medium text-slate-800">{{ $ticket->created_at->format('M j, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Last updated</span>
                    <span class="font-medium text-slate-800">{{ $ticket->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Time entries link --}}
            <a href="{{ route('admin.time-entries.index', $ticket) }}"
               class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50 transition text-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Time entries</p>
                        <p class="text-xs text-slate-400">Log & view time spent</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            {{-- Merge tickets --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Merge tickets</p>
                <p class="text-xs text-slate-400 mb-3">Enter comma-separated ticket IDs to merge into this ticket. Those tickets will be closed and their replies moved here.</p>
                <form method="POST" action="{{ route('admin.tickets.merge') }}"
                      onsubmit="return confirm('Merge selected tickets into #{{ $ticket->id }}? Source tickets will be closed.')">
                    @csrf
                    <input type="hidden" name="target_ticket_id" value="{{ $ticket->id }}">
                    <div class="space-y-2">
                        <label for="merge_ids" class="block text-xs font-medium text-slate-500">Source ticket IDs</label>
                        <input type="text" id="merge_ids" name="merge_ids_raw" placeholder="e.g. 12, 15, 20"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <p class="text-xs text-slate-400">Separate multiple IDs with commas</p>
                    </div>
                    <button type="submit" class="mt-3 w-full rounded-xl border border-slate-300 bg-slate-50 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                        Merge into this ticket
                    </button>
                </form>
            </div>
        </aside>
    </div>
@endsection
