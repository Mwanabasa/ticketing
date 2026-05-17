@extends('layouts.app')

@section('title', 'All tickets')
@section('page_title', 'Help desk inbox')

@push('head')
<script>
function updateBulkBar() {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    const bar     = document.getElementById('bulk-bar');
    const count   = document.getElementById('bulk-count');
    const ids     = document.getElementById('bulk-ids');

    ids.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ticket_ids[]';
        inp.value = cb.dataset.id;
        ids.appendChild(inp);
    });

    bar.classList.toggle('hidden', checked.length === 0);
    count.textContent = checked.length + ' selected';
}

function clearSelection() {
    document.querySelectorAll('.ticket-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('bulk-bar').classList.add('hidden');
    document.getElementById('bulk-ids').innerHTML = '';
}
</script>
@endpush

@section('content')
    @php
        $inboxViews = [
            ['label' => 'Assigned to me', 'count' => $viewCounts['assigned_to_me'], 'params' => ['assigned_to' => auth()->id(), 'status' => null]],
            ['label' => 'All open',       'count' => $viewCounts['all_open'],        'params' => ['status' => 'open',      'assigned_to' => null]],
            ['label' => 'Unassigned',     'count' => $viewCounts['unassigned'],      'params' => ['assigned_to' => 'unassigned', 'status' => null]],
            ['label' => 'Pending',        'count' => $viewCounts['pending'],         'params' => ['status' => 'pending',   'assigned_to' => null]],
            ['label' => 'Resolved',       'count' => $viewCounts['resolved'],        'params' => ['status' => 'resolved',  'assigned_to' => null]],
            ['label' => 'Closed',         'count' => $viewCounts['closed'],          'params' => ['status' => 'closed',    'assigned_to' => null]],
        ];

        $statusTabs = [
            ['label' => 'All',      'value' => null],
            ['label' => 'Open',     'value' => 'open'],
            ['label' => 'Pending',  'value' => 'pending'],
            ['label' => 'Resolved', 'value' => 'resolved'],
            ['label' => 'Closed',   'value' => 'closed'],
        ];
    @endphp

    {{-- ── LAYOUT: sidebar + main ─────────────────────────────────────── --}}
    <div class="flex gap-5 items-start">

        {{-- ── LEFT SIDEBAR ──────────────────────────────────────────────── --}}
        <aside class="hidden lg:flex lg:flex-col w-56 shrink-0 rounded-2xl border border-slate-200 bg-white shadow-sm sticky top-6">
            <div class="border-b border-slate-200 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Views</p>
                <p class="mt-0.5 text-base font-bold text-slate-900">Inbox</p>
            </div>
            <nav class="space-y-0.5 p-2">
                @foreach ($inboxViews as $view)
                    @php
                        $params = request()->except('page', 'ticket');
                        foreach ($view['params'] as $key => $value) {
                            if ($value === null) unset($params[$key]);
                            else $params[$key] = $value;
                        }
                        $isActive = true;
                        foreach ($view['params'] as $key => $value) {
                            if ($value === null) $isActive = $isActive && ! request()->filled($key);
                            else $isActive = $isActive && (string) request($key) === (string) $value;
                        }
                    @endphp
                    <a href="{{ route('admin.tickets.index', $params) }}"
                       class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium transition
                              {{ $isActive ? 'bg-indigo-50 text-indigo-800 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span>{{ $view['label'] }}</span>
                        <span class="text-xs font-bold {{ $isActive ? 'text-indigo-600' : 'text-slate-400' }}">{{ $view['count'] }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="border-t border-slate-200 p-3">
                <a href="{{ route('admin.tickets.index') }}"
                   class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Clear filters
                </a>
            </div>
        </aside>

        {{-- ── MAIN COLUMN ────────────────────────────────────────────────── --}}
        <div class="min-w-0 flex-1 space-y-4">

            {{-- Header + status tabs --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-bold text-slate-900">All Tickets</h2>
                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-700">{{ $tickets->total() }}</span>
                    </div>
                    {{-- Status tabs --}}
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($statusTabs as $tab)
                            @php
                                $isActive = $tab['value'] === null
                                    ? ! request()->filled('status')
                                    : request('status') === $tab['value'];
                                $url = $tab['value'] === null
                                    ? route('admin.tickets.index', request()->except('status', 'page', 'ticket'))
                                    : route('admin.tickets.index', array_merge(request()->except('page', 'ticket'), ['status' => $tab['value']]));
                            @endphp
                            <a href="{{ $url }}"
                               class="rounded-lg px-3 py-1.5 text-xs font-semibold transition
                                      {{ $isActive ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $tab['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Search + filter bar --}}
                <div class="px-5 py-3 border-b border-slate-100">
                    <form method="GET" action="{{ route('admin.tickets.index') }}"
                          class="flex flex-wrap gap-2">
                        <input name="q" type="search" value="{{ request('q') }}"
                               placeholder="Search subject, description, or #ID…"
                               class="flex-1 min-w-[180px] rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <select name="category_id"
                                class="rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select name="assigned_to"
                                class="rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="">All assignees</option>
                            <option value="unassigned" @selected(request('assigned_to') === 'unassigned')>Unassigned</option>
                            @foreach ($staff as $member)
                                <option value="{{ $member->id }}" @selected((string) request('assigned_to') === (string) $member->id)>{{ $member->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                            Filter
                        </button>
                        @if (request()->hasAny(['q', 'status', 'category_id', 'assigned_to']))
                            <a href="{{ route('admin.tickets.index') }}"
                               class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Bulk action bar --}}
                <div id="bulk-bar" class="hidden border-b border-amber-200 bg-amber-50 px-5 py-3">
                    <form method="POST" action="{{ route('admin.tickets.bulk') }}" id="bulk-form">
                        @csrf
                        <div id="bulk-ids"></div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span id="bulk-count" class="text-sm font-semibold text-amber-800">0 selected</span>
                            <select name="action"
                                    class="rounded-xl border border-amber-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none">
                                <option value="assign_status">Change status</option>
                                <option value="assign_priority">Change priority</option>
                                <option value="close">Close tickets</option>
                            </select>
                            <select name="status"
                                    class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none">
                                @foreach (\App\Enums\TicketStatus::cases() as $s)
                                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                @endforeach
                            </select>
                            <select name="priority"
                                    class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none">
                                @foreach (\App\Enums\TicketPriority::cases() as $p)
                                    <option value="{{ $p->value }}">{{ $p->label() }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition">
                                Apply
                            </button>
                            <button type="button" onclick="clearSelection()"
                                    class="text-sm font-medium text-amber-700 hover:underline">
                                Clear
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Ticket rows --}}
                <div class="divide-y divide-slate-100">
                    @forelse ($tickets as $ticket)
                        @php
                            $isSelected   = $previewTicket?->id === $ticket->id;
                            $previewParams = array_merge(request()->except('page'), ['ticket' => $ticket->id]);
                        @endphp
                        <div class="flex items-start gap-3 px-5 py-4 transition hover:bg-slate-50
                                    {{ $isSelected ? 'bg-indigo-50 ring-1 ring-inset ring-indigo-200' : '' }}">

                            {{-- Checkbox --}}
                            <div class="pt-1 shrink-0">
                                <input type="checkbox"
                                       class="ticket-checkbox h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                       data-id="{{ $ticket->id }}"
                                       onchange="updateBulkBar()">
                            </div>

                            {{-- Ticket info --}}
                            <a href="{{ route('admin.tickets.index', $previewParams) }}"
                               class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">
                                        {{ $ticket->priority->label() }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1.5 truncate text-sm font-semibold text-slate-900">{{ $ticket->subject }}</p>
                                <p class="mt-0.5 line-clamp-1 text-xs text-slate-500">{{ $ticket->description }}</p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $ticket->user->name }}</span>
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $ticket->category->name }}</span>
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs {{ $ticket->assignee ? 'text-indigo-700 bg-indigo-50' : 'text-slate-400' }}">
                                        {{ $ticket->assignee?->name ?? 'Unassigned' }}
                                    </span>
                                </div>
                            </a>

                            {{-- Quick-edit controls (desktop only) --}}
                            <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}"
                                  class="hidden xl:flex items-center gap-2 shrink-0">
                                @csrf @method('PATCH')
                                <select name="status"
                                        class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs shadow-sm focus:border-indigo-400 focus:outline-none">
                                    @foreach (\App\Enums\TicketStatus::cases() as $s)
                                        <option value="{{ $s->value }}" @selected($ticket->status === $s)>{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                                <select name="priority"
                                        class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs shadow-sm focus:border-indigo-400 focus:outline-none">
                                    @foreach (\App\Enums\TicketPriority::cases() as $p)
                                        <option value="{{ $p->value }}" @selected($ticket->priority === $p)>{{ $p->label() }}</option>
                                    @endforeach
                                </select>
                                <select name="assigned_to"
                                        class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs shadow-sm focus:border-indigo-400 focus:outline-none">
                                    <option value="">Unassigned</option>
                                    @foreach ($staff as $member)
                                        <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700 transition">
                                    Save
                                </button>
                            </form>

                            {{-- Open button (always visible) --}}
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                                Open
                            </a>
                        </div>
                    @empty
                        <div class="px-5 py-16 text-center">
                            <p class="font-semibold text-slate-700">No tickets match your filters.</p>
                            <p class="mt-1 text-sm text-slate-400">Try clearing filters or searching a different term.</p>
                            <a href="{{ route('admin.tickets.index') }}"
                               class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Clear all filters
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-200 px-5 py-3">{{ $tickets->links() }}</div>
            </div>
        </div>

        {{-- ── RIGHT PREVIEW PANEL ────────────────────────────────────────── --}}
        <aside class="hidden xl:flex xl:flex-col w-80 shrink-0 rounded-2xl border border-slate-200 bg-white shadow-sm sticky top-6">
            <div class="border-b border-slate-200 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Quick preview</p>
                <p class="mt-0.5 text-base font-bold text-slate-900">Ticket detail</p>
            </div>

            @if ($previewTicket)
                <div class="flex-1 overflow-y-auto p-5 space-y-5">
                    {{-- Title & badges --}}
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-400">#{{ $previewTicket->id }}</span>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $previewTicket->status->badgeClass() }}">
                                {{ $previewTicket->status->label() }}
                            </span>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $previewTicket->priority->badgeClass() }}">
                                {{ $previewTicket->priority->label() }}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $previewTicket->subject }}</h3>
                        <p class="mt-1.5 text-xs text-slate-500 leading-relaxed line-clamp-4">{{ $previewTicket->description }}</p>
                    </div>

                    {{-- Meta --}}
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <dt class="font-semibold text-slate-500">Student</dt>
                            <dd class="text-slate-800 font-medium">{{ $previewTicket->user->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-semibold text-slate-500">Category</dt>
                            <dd class="text-slate-800 font-medium">{{ $previewTicket->category->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-semibold text-slate-500">Assignee</dt>
                            <dd class="text-slate-800 font-medium">{{ $previewTicket->assignee?->name ?? 'Unassigned' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-semibold text-slate-500">Opened</dt>
                            <dd class="text-slate-500">{{ $previewTicket->created_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>

                    {{-- Quick update --}}
                    <form method="POST" action="{{ route('admin.tickets.update', $previewTicket) }}"
                          class="rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-2">
                        @csrf @method('PATCH')
                        <p class="text-xs font-semibold text-slate-700 mb-1">Quick update</p>
                        <select name="status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs shadow-sm focus:border-indigo-400 focus:outline-none">
                            @foreach (\App\Enums\TicketStatus::cases() as $s)
                                <option value="{{ $s->value }}" @selected($previewTicket->status === $s)>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <select name="priority"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs shadow-sm focus:border-indigo-400 focus:outline-none">
                            @foreach (\App\Enums\TicketPriority::cases() as $p)
                                <option value="{{ $p->value }}" @selected($previewTicket->priority === $p)>{{ $p->label() }}</option>
                            @endforeach
                        </select>
                        <select name="assigned_to"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs shadow-sm focus:border-indigo-400 focus:outline-none">
                            <option value="">Unassigned</option>
                            @foreach ($staff as $member)
                                <option value="{{ $member->id }}" @selected($previewTicket->assigned_to === $member->id)>{{ $member->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="w-full rounded-lg bg-indigo-600 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                            Save changes
                        </button>
                    </form>

                    {{-- Latest replies --}}
                    <div>
                        <p class="text-xs font-semibold text-slate-700 mb-2">Latest replies</p>
                        <div class="space-y-2">
                            @forelse ($previewTicket->replies->take(-3) as $reply)
                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-semibold text-slate-800">{{ $reply->user->name }}</span>
                                        <span class="text-xs text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">{{ $reply->body }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">No replies yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <a href="{{ route('admin.tickets.show', $previewTicket) }}"
                       class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Open full ticket →
                    </a>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center p-8 text-center">
                    <div>
                        <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">No ticket selected</p>
                        <p class="mt-1 text-xs text-slate-400">Click a ticket row to preview it here.</p>
                    </div>
                </div>
            @endif
        </aside>

    </div>
@endsection
