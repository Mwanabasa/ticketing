@extends('layouts.app')

@section('title', 'Tickets')
@section('page_title', 'Tickets')
@section('page_subtitle', 'Manage and respond to support requests')

@push('head')
<script>
function updateBulkBar() {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    const bar = document.getElementById('bulk-bar');
    const count = document.getElementById('bulk-count');
    const ids = document.getElementById('bulk-ids');
    ids.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ticket_ids[]'; inp.value = cb.dataset.id;
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
        ['label' => 'Assigned to me', 'count' => $viewCounts['assigned_to_me'], 'dot' => 'bg-indigo-500', 'params' => ['assigned_to' => auth()->id(), 'status' => null]],
        ['label' => 'All open',       'count' => $viewCounts['all_open'],        'dot' => 'bg-emerald-500','params' => ['status' => 'open', 'assigned_to' => null]],
        ['label' => 'Unassigned',     'count' => $viewCounts['unassigned'],      'dot' => 'bg-rose-500',   'params' => ['assigned_to' => 'unassigned', 'status' => null]],
        ['label' => 'Pending',        'count' => $viewCounts['pending'],         'dot' => 'bg-amber-500',  'params' => ['status' => 'pending', 'assigned_to' => null]],
        ['label' => 'Resolved',       'count' => $viewCounts['resolved'],        'dot' => 'bg-blue-500',   'params' => ['status' => 'resolved', 'assigned_to' => null]],
        ['label' => 'Closed',         'count' => $viewCounts['closed'],          'dot' => 'bg-slate-400',  'params' => ['status' => 'closed', 'assigned_to' => null]],
    ];
    $statusTabs = [
        ['label' => 'All', 'value' => null],
        ['label' => 'Open', 'value' => 'open'],
        ['label' => 'Pending', 'value' => 'pending'],
        ['label' => 'Resolved', 'value' => 'resolved'],
        ['label' => 'Closed', 'value' => 'closed'],
    ];
@endphp

<div class="flex gap-4 items-start">

    {{-- LEFT SIDEBAR --}}
    <aside class="hidden lg:flex lg:flex-col w-52 shrink-0 card sticky top-4">
        <div class="px-4 py-4 border-b border-slate-100">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Inbox views</p>
        </div>
        <nav class="p-2 space-y-0.5">
            @foreach ($inboxViews as $view)
                @php
                    $params = request()->except('page','ticket');
                    foreach ($view['params'] as $k => $v) {
                        if ($v === null) unset($params[$k]); else $params[$k] = $v;
                    }
                    $isActive = true;
                    foreach ($view['params'] as $k => $v) {
                        if ($v === null) $isActive = $isActive && !request()->filled($k);
                        else $isActive = $isActive && (string)request($k) === (string)$v;
                    }
                @endphp
                <a href="{{ route('admin.tickets.index', $params) }}"
                   class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition
                          {{ $isActive ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="w-2 h-2 rounded-full {{ $view['dot'] }} shrink-0 {{ $isActive ? 'opacity-100' : 'opacity-60' }}"></span>
                    <span class="flex-1 font-medium">{{ $view['label'] }}</span>
                    <span class="text-xs font-bold {{ $isActive ? 'text-indigo-200' : 'text-slate-400' }}">{{ $view['count'] }}</span>
                </a>
            @endforeach
        </nav>
        <div class="p-3 border-t border-slate-100">
            <a href="{{ route('admin.tickets.index') }}"
               class="block w-full text-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-50 transition">
                Clear filters
            </a>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 min-w-0 space-y-3">

        {{-- Toolbar --}}
        <div class="card">

            {{-- Top: title + status tabs --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="font-bold text-slate-900">All Tickets</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">{{ $tickets->total() }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($statusTabs as $tab)
                        @php
                            $active = $tab['value'] === null ? !request()->filled('status') : request('status') === $tab['value'];
                            $url = $tab['value'] === null
                                ? route('admin.tickets.index', request()->except('status','page','ticket'))
                                : route('admin.tickets.index', array_merge(request()->except('page','ticket'), ['status' => $tab['value']]));
                        @endphp
                        <a href="{{ $url }}"
                           class="rounded-lg px-3 py-1.5 text-xs font-semibold transition
                                  {{ $active ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Search + filters --}}
            <div class="px-5 py-3 border-b border-slate-100">
                <form method="GET" action="{{ route('admin.tickets.index') }}" class="flex flex-wrap gap-2">
                    <div class="relative flex-1 min-w-[180px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input name="q" type="search" value="{{ request('q') }}" placeholder="Search tickets…"
                               class="w-full pl-9 pr-3 py-2 rounded-lg border border-slate-200 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <select name="category_id" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <option value="">All categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string)request('category_id') === (string)$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select name="assigned_to" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <option value="">All assignees</option>
                        <option value="unassigned" @selected(request('assigned_to') === 'unassigned')>Unassigned</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}" @selected((string)request('assigned_to') === (string)$member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary text-xs px-4 py-2">Filter</button>
                    @if (request()->hasAny(['q','status','category_id','assigned_to']))
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary text-xs px-4 py-2">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Bulk bar --}}
            <div id="bulk-bar" class="hidden px-5 py-3 border-b border-amber-200 bg-amber-50">
                <form method="POST" action="{{ route('admin.tickets.bulk') }}">
                    @csrf
                    <div id="bulk-ids"></div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="bulk-count" class="text-sm font-semibold text-amber-800">0 selected</span>
                        <select name="action" class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-sm">
                            <option value="assign_status">Change status</option>
                            <option value="assign_priority">Change priority</option>
                            <option value="close">Close tickets</option>
                        </select>
                        <select name="status" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm">
                            @foreach (\App\Enums\TicketStatus::cases() as $s)
                                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm">
                            @foreach (\App\Enums\TicketPriority::cases() as $p)
                                <option value="{{ $p->value }}">{{ $p->label() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-lg bg-amber-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-amber-700 transition">Apply</button>
                        <button type="button" onclick="clearSelection()" class="text-sm font-medium text-amber-700 hover:underline">Cancel</button>
                    </div>
                </form>
            </div>

            {{-- Ticket rows --}}
            <div class="divide-y divide-slate-50">
                @forelse ($tickets as $ticket)
                    @php
                        $isSelected = $previewTicket?->id === $ticket->id;
                        $previewParams = array_merge(request()->except('page'), ['ticket' => $ticket->id]);
                    @endphp
                    <div class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50 transition {{ $isSelected ? 'bg-indigo-50/60 border-l-2 border-indigo-500' : '' }}">

                        <input type="checkbox" class="ticket-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 shrink-0"
                               data-id="{{ $ticket->id }}" onchange="updateBulkBar()">

                        <a href="{{ route('admin.tickets.index', $previewParams) }}" class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                <span class="text-xs font-mono text-slate-400">#{{ $ticket->id }}</span>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $ticket->status->badgeClass() }}">{{ $ticket->status->label() }}</span>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">{{ $ticket->priority->label() }}</span>
                                @if ($ticket->isOverdue())
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-700">⚠ Overdue</span>
                                @endif
                            </div>
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $ticket->subject }}</p>
                            <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $ticket->description }}</p>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $ticket->user->name }}</span>
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $ticket->category->name }}</span>
                                <span class="rounded-md px-2 py-0.5 text-xs {{ $ticket->assignee ? 'bg-indigo-50 text-indigo-700' : 'bg-rose-50 text-rose-500' }}">
                                    {{ $ticket->assignee?->name ?? 'Unassigned' }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </a>

                        {{-- Quick edit (xl+) --}}
                        <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}"
                              class="hidden xl:flex items-center gap-1.5 shrink-0">
                            @csrf @method('PATCH')
                            <select name="status" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs focus:outline-none focus:border-indigo-400">
                                @foreach (\App\Enums\TicketStatus::cases() as $s)
                                    <option value="{{ $s->value }}" @selected($ticket->status === $s)>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                            <select name="priority" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs focus:outline-none focus:border-indigo-400">
                                @foreach (\App\Enums\TicketPriority::cases() as $p)
                                    <option value="{{ $p->value }}" @selected($ticket->priority === $p)>{{ $p->label() }}</option>
                                @endforeach
                            </select>
                            <select name="assigned_to" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs focus:outline-none focus:border-indigo-400">
                                <option value="">Unassigned</option>
                                @foreach ($staff as $member)
                                    <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700 transition">Save</button>
                        </form>

                        <a href="{{ route('admin.tickets.show', $ticket) }}"
                           class="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:border-slate-300 transition">
                            Open →
                        </a>
                    </div>
                @empty
                    <div class="py-20 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-100 mb-4">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <p class="font-semibold text-slate-700">No tickets found</p>
                        <p class="mt-1 text-sm text-slate-400">Try adjusting your filters.</p>
                        <a href="{{ route('admin.tickets.index') }}" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Clear filters</a>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-slate-100 px-5 py-3">{{ $tickets->links() }}</div>
        </div>
    </div>

    {{-- RIGHT PREVIEW --}}
    <aside class="hidden xl:flex xl:flex-col w-72 shrink-0 card sticky top-4 max-h-[calc(100vh-6rem)] overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Preview</p>
            <p class="font-bold text-slate-900 mt-0.5">Ticket detail</p>
        </div>

        @if ($previewTicket)
            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                <div>
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        <span class="text-xs font-mono text-slate-400">#{{ $previewTicket->id }}</span>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $previewTicket->status->badgeClass() }}">{{ $previewTicket->status->label() }}</span>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $previewTicket->priority->badgeClass() }}">{{ $previewTicket->priority->label() }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $previewTicket->subject }}</h3>
                    <p class="mt-1.5 text-xs text-slate-500 leading-relaxed line-clamp-3">{{ $previewTicket->description }}</p>
                </div>

                <dl class="space-y-2 text-xs border-t border-slate-100 pt-3">
                    @foreach ([
                        ['Student',  $previewTicket->user->name],
                        ['Category', $previewTicket->category->name],
                        ['Assignee', $previewTicket->assignee?->name ?? 'Unassigned'],
                        ['Opened',   $previewTicket->created_at->format('M j, Y')],
                    ] as [$label, $value])
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-400 shrink-0">{{ $label }}</dt>
                            <dd class="font-medium text-slate-800 text-right truncate">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                <form method="POST" action="{{ route('admin.tickets.update', $previewTicket) }}"
                      class="space-y-2 border-t border-slate-100 pt-3">
                    @csrf @method('PATCH')
                    <p class="text-xs font-semibold text-slate-700">Quick update</p>
                    <select name="status" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
                        @foreach (\App\Enums\TicketStatus::cases() as $s)
                            <option value="{{ $s->value }}" @selected($previewTicket->status === $s)>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <select name="priority" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
                        @foreach (\App\Enums\TicketPriority::cases() as $p)
                            <option value="{{ $p->value }}" @selected($previewTicket->priority === $p)>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                    <select name="assigned_to" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
                        <option value="">Unassigned</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}" @selected($previewTicket->assigned_to === $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">Save changes</button>
                </form>

                <div class="border-t border-slate-100 pt-3">
                    <p class="text-xs font-semibold text-slate-700 mb-2">Latest replies</p>
                    <div class="space-y-2">
                        @forelse ($previewTicket->replies->take(-3) as $reply)
                            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold text-slate-800">{{ $reply->user->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2">{{ $reply->body }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">No replies yet.</p>
                        @endforelse
                    </div>
                </div>

                <a href="{{ route('admin.tickets.show', $previewTicket) }}"
                   class="flex items-center justify-center gap-1.5 w-full rounded-lg border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Open full ticket →
                </a>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center p-8 text-center">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">No ticket selected</p>
                    <p class="mt-1 text-xs text-slate-400">Click a row to preview.</p>
                </div>
            </div>
        @endif
    </aside>

</div>
@endsection
