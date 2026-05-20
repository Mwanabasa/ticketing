@extends('layouts.app')
@section('title', 'Tickets')
@section('page_title', 'Tickets')
@section('page_subtitle', 'Manage and respond to support requests')

@push('scripts')
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
function setView(v) {
    localStorage.setItem('ticketView', v);
    document.getElementById('view-list').classList.toggle('hidden', v !== 'list');
    document.getElementById('view-grid').classList.toggle('hidden', v !== 'grid');
    document.getElementById('btn-list').classList.toggle('active-view', v === 'list');
    document.getElementById('btn-grid').classList.toggle('active-view', v === 'grid');
}
document.addEventListener('DOMContentLoaded', () => setView(localStorage.getItem('ticketView') || 'list'));
</script>
<style>
/* Priority colours */
.pri-low    { color:#6b7280;font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.04em; }
.pri-medium { color:#2563eb;font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.04em; }
.pri-high   { color:#d97706;font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.04em; }
.pri-urgent { color:#dc2626;font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.04em; }
/* Avatar */
.av { border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;color:#fff;flex-shrink:0; }
/* View toggle active */
.active-view { background:#4f46e5!important;color:#fff!important;border-color:#4f46e5!important; }
/* Stat tab active */
.stat-tab-active { border-bottom:2px solid #4f46e5;color:#4f46e5!important; }
/* Ticket card */
.tcard { background:#fff;border-radius:12px;border:1px solid #e8eaf2;box-shadow:0 1px 3px rgba(0,0,0,.04);transition:box-shadow .15s,transform .15s;overflow:hidden; }
.tcard:hover { box-shadow:0 4px 16px rgba(79,70,229,.1);transform:translateY(-2px); }
.tcard-bar { height:3px;width:100%; }
</style>
@endpush

@section('content')
@php
    $statusColor = ['open'=>'#10b981','pending'=>'#f59e0b','resolved'=>'#3b82f6','closed'=>'#94a3b8'];
    $avatarColors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444'];
    $statTabs = [
        ['label'=>'Waiting for me', 'count'=>$viewCounts['assigned_to_me'], 'color'=>'#6366f1',
         'params'=>['assigned_to'=>auth()->id(),'status'=>null]],
        ['label'=>'All open',       'count'=>$viewCounts['all_open'],        'color'=>'#10b981',
         'params'=>['status'=>'open','assigned_to'=>null]],
        ['label'=>'Unassigned',     'count'=>$viewCounts['unassigned'],      'color'=>'#ef4444',
         'params'=>['assigned_to'=>'unassigned','status'=>null]],
        ['label'=>'Pending',        'count'=>$viewCounts['pending'],         'color'=>'#f59e0b',
         'params'=>['status'=>'pending','assigned_to'=>null]],
        ['label'=>'Resolved',       'count'=>$viewCounts['resolved'],        'color'=>'#3b82f6',
         'params'=>['status'=>'resolved','assigned_to'=>null]],
        ['label'=>'Closed',         'count'=>$viewCounts['closed'],          'color'=>'#94a3b8',
         'params'=>['status'=>'closed','assigned_to'=>null]],
    ];
@endphp

{{-- ── STAT TABS (replaces sidebar queue list) ──────────────────────────── --}}
<div class="card mb-4 overflow-hidden">
    <div class="flex overflow-x-auto" style="border-bottom:1px solid #f1f5f9;">
        @foreach ($statTabs as $tab)
            @php
                $params = request()->except('page','ticket');
                foreach ($tab['params'] as $k => $v) { if ($v===null) unset($params[$k]); else $params[$k]=$v; }
                $isActive = true;
                foreach ($tab['params'] as $k => $v) {
                    if ($v===null) $isActive = $isActive && !request()->filled($k);
                    else $isActive = $isActive && (string)request($k)===(string)$v;
                }
            @endphp
            <a href="{{ route('admin.tickets.index', $params) }}"
               class="flex items-center gap-2.5 px-5 py-3.5 whitespace-nowrap text-sm font-semibold transition-colors shrink-0
                      {{ $isActive ? 'stat-tab-active' : '' }}"
               style="color:{{ $isActive ? $tab['color'] : '#64748b' }};border-bottom:{{ $isActive ? '2px solid '.$tab['color'] : '2px solid transparent' }};">
                <span class="text-xl font-extrabold tabular-nums" style="color:{{ $tab['color'] }};">{{ $tab['count'] }}</span>
                <span class="text-xs font-semibold" style="color:{{ $isActive ? $tab['color'] : '#94a3b8' }};">{{ $tab['label'] }}</span>
            </a>
        @endforeach
        <div class="flex-1"></div>
        {{-- View toggle --}}
        <div class="flex items-center gap-1 px-4 shrink-0">
            <button id="btn-list" onclick="setView('list')"
                    class="flex items-center justify-center w-8 h-8 rounded-lg border transition-colors"
                    style="background:#f8fafc;color:#64748b;border-color:#e2e8f0;" title="List view">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </button>
            <button id="btn-grid" onclick="setView('grid')"
                    class="flex items-center justify-center w-8 h-8 rounded-lg border transition-colors"
                    style="background:#f8fafc;color:#64748b;border-color:#e2e8f0;" title="Card view">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
        </div>
    </div>
</div>

{{-- ── MAIN CONTENT AREA ─────────────────────────────────────────────────── --}}
<div class="flex gap-4 items-start">
<div class="flex-1 min-w-0 space-y-3">

{{-- Search + filter bar --}}
<div class="card">
    <div class="px-5 py-3" style="border-bottom:1px solid #f1f5f9;">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="flex flex-wrap gap-2 items-center">
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="q" type="search" value="{{ request('q') }}" placeholder="Search tickets…" class="input pl-9 py-2 text-sm">
            </div>
            <select name="category_id" class="input px-3 py-2 text-sm w-auto">
                <option value="">All categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string)request('category_id')===(string)$cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="assigned_to" class="input px-3 py-2 text-sm w-auto">
                <option value="">All assignees</option>
                <option value="unassigned" @selected(request('assigned_to')==='unassigned')>Unassigned</option>
                @foreach ($staff as $member)
                    <option value="{{ $member->id }}" @selected((string)request('assigned_to')===(string)$member->id)>{{ $member->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if (request()->hasAny(['q','status','category_id','assigned_to']))
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary btn-sm">Clear</a>
            @endif
            <span class="text-xs ml-auto" style="color:#94a3b8;">
                {{ $tickets->total() }} ticket{{ $tickets->total() !== 1 ? 's' : '' }}
            </span>
        </form>
    </div>

    {{-- Bulk bar --}}
    <div id="bulk-bar" class="hidden px-5 py-3" style="border-bottom:1px solid #fde68a;background:#fffbeb;">
        <form method="POST" action="{{ route('admin.tickets.bulk') }}">
            @csrf
            <div id="bulk-ids"></div>
            <div class="flex flex-wrap items-center gap-2">
                <span id="bulk-count" class="text-sm font-semibold" style="color:#92400e;">0 selected</span>
                <select name="action" class="input px-3 py-1.5 text-sm w-auto" style="border-color:#fcd34d;">
                    <option value="assign_status">Change status</option>
                    <option value="assign_priority">Change priority</option>
                    <option value="close">Close tickets</option>
                </select>
                <select name="status" class="input px-3 py-1.5 text-sm w-auto">
                    @foreach (\App\Enums\TicketStatus::cases() as $s)<option value="{{ $s->value }}">{{ $s->label() }}</option>@endforeach
                </select>
                <select name="priority" class="input px-3 py-1.5 text-sm w-auto">
                    @foreach (\App\Enums\TicketPriority::cases() as $p)<option value="{{ $p->value }}">{{ $p->label() }}</option>@endforeach
                </select>
                <button type="submit" class="btn btn-sm" style="background:#d97706;color:#fff;">Apply</button>
                <button type="button" onclick="clearSelection()" class="text-sm font-medium" style="color:#92400e;">Cancel</button>
            </div>
        </form>
    </div>

    {{-- ── LIST VIEW ──────────────────────────────────────────────────────── --}}
    <div id="view-list">
        {{-- Column headers --}}
        <div class="hidden md:grid px-5 py-2 text-[10px] font-bold uppercase tracking-widest"
             style="grid-template-columns:4px 20px 1fr 130px 110px 80px 70px;gap:10px;color:#94a3b8;border-bottom:1px solid #f1f5f9;">
            <span></span><span></span>
            <span>Ticket</span><span>Raised by</span><span>Assigned to</span>
            <span class="text-right">Priority</span><span></span>
        </div>
        <div class="divide-y" style="divide-color:#f8fafc;">
            @forelse ($tickets as $ticket)
                @php
                    $isSelected = $previewTicket?->id === $ticket->id;
                    $previewParams = array_merge(request()->except('page'), ['ticket' => $ticket->id]);
                    $bc = $statusColor[$ticket->status->value] ?? '#94a3b8';
                    $sc = $avatarColors[crc32($ticket->user->name) % count($avatarColors)];
                    $ac = $ticket->assignee ? $avatarColors[crc32($ticket->assignee->name) % count($avatarColors)] : null;
                @endphp
                <div class="flex items-center gap-2.5 px-5 py-3 transition-colors hover:bg-indigo-50/20
                            {{ $isSelected ? 'bg-indigo-50/40' : '' }}"
                     style="{{ $isSelected ? 'border-left:3px solid #6366f1;' : 'border-left:3px solid '.$bc.';' }}">
                    <div class="w-0.5 self-stretch rounded-full shrink-0" style="background:{{ $bc }};min-height:32px;display:none;"></div>
                    <input type="checkbox" class="ticket-checkbox h-3.5 w-3.5 rounded shrink-0" style="border-color:#d1d5db;" data-id="{{ $ticket->id }}" onchange="updateBulkBar()">
                    <a href="{{ route('admin.tickets.index', $previewParams) }}" class="flex-1 min-w-0 flex items-center gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-0.5 flex-wrap">
                                <span class="text-[10px] font-mono font-bold" style="color:#94a3b8;">#{{ $ticket->id }}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background:{{ $bc }}18;color:{{ $bc }};">{{ $ticket->status->label() }}</span>
                                @if ($ticket->isOverdue())<span class="text-[10px] font-bold" style="color:#dc2626;">⚠ OVERDUE</span>@endif
                            </div>
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->subject }}</p>
                            <p class="text-xs truncate mt-0.5" style="color:#94a3b8;">{{ Str::limit($ticket->description, 65) }}</p>
                        </div>
                        <div class="hidden md:flex items-center gap-1.5 w-32 shrink-0">
                            <div class="av shrink-0" style="background:{{ $sc }};width:22px;height:22px;font-size:9px;">{{ strtoupper(substr($ticket->user->name,0,1)) }}</div>
                            <span class="text-xs font-medium text-gray-600 truncate">{{ Str::limit($ticket->user->name,12) }}</span>
                        </div>
                        <div class="hidden md:flex items-center gap-1.5 w-28 shrink-0">
                            @if ($ticket->assignee)
                                <div class="av shrink-0" style="background:{{ $ac }};width:22px;height:22px;font-size:9px;">{{ strtoupper(substr($ticket->assignee->name,0,1)) }}</div>
                                <span class="text-xs font-medium text-gray-600 truncate">{{ Str::limit($ticket->assignee->name,10) }}</span>
                            @else
                                <span class="text-xs font-semibold" style="color:#ef4444;">Unassigned</span>
                            @endif
                        </div>
                        <div class="hidden md:block w-16 text-right shrink-0">
                            <span class="pri-{{ $ticket->priority->value }}">{{ $ticket->priority->label() }}</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-secondary btn-xs shrink-0">Open →</a>
                </div>
            @empty
                <div class="py-20 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-4" style="background:#f1f5f9;">
                        <svg class="w-7 h-7" style="color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <p class="font-bold text-gray-700">No tickets found</p>
                    <p class="mt-1 text-sm text-gray-400">Try adjusting your filters.</p>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-primary mt-4">Clear filters</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── CARD GRID VIEW ─────────────────────────────────────────────────── --}}
    <div id="view-grid" class="hidden p-4">
        @if ($tickets->isEmpty())
            <div class="py-16 text-center col-span-full">
                <p class="font-bold text-gray-700">No tickets found</p>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-primary mt-4">Clear filters</a>
            </div>
        @else
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($tickets as $ticket)
                    @php
                        $bc = $statusColor[$ticket->status->value] ?? '#94a3b8';
                        $sc = $avatarColors[crc32($ticket->user->name) % count($avatarColors)];
                        $ac = $ticket->assignee ? $avatarColors[crc32($ticket->assignee->name) % count($avatarColors)] : null;
                        $priColors = ['low'=>['bg'=>'#f3f4f6','text'=>'#6b7280'],'medium'=>['bg'=>'#dbeafe','text'=>'#1d4ed8'],'high'=>['bg'=>'#fef3c7','text'=>'#b45309'],'urgent'=>['bg'=>'#fee2e2','text'=>'#b91c1c']];
                        $pc = $priColors[$ticket->priority->value] ?? $priColors['low'];
                    @endphp
                    <div class="tcard">
                        {{-- Coloured top bar --}}
                        <div class="tcard-bar" style="background:{{ $bc }};"></div>

                        <div class="p-4">
                            {{-- Header: AGENT chip + Priority tag --}}
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded"
                                          style="background:#e0e7ff;color:#4338ca;">AGENT</span>
                                    @if ($ticket->assignee)
                                        <span class="text-[10px] font-semibold" style="color:#4338ca;">{{ Str::limit($ticket->assignee->name, 14) }}</span>
                                    @else
                                        <span class="text-[10px] font-semibold" style="color:#ef4444;">Unassigned</span>
                                    @endif
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full"
                                      style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};">
                                    {{ $ticket->priority->label() }}
                                </span>
                            </div>

                            {{-- Ticket ID + subject --}}
                            <p class="text-[10px] font-mono font-bold mb-1" style="color:#94a3b8;">#{{ $ticket->id }}</p>
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition-colors leading-snug line-clamp-2 block mb-2">
                                {{ $ticket->subject }}
                            </a>

                            {{-- Category path --}}
                            <p class="text-xs mb-3 truncate" style="color:#94a3b8;">
                                {{ $ticket->user->name }} › {{ $ticket->category->name }}
                            </p>

                            {{-- Status badge --}}
                            <span class="inline-flex text-[10px] font-bold px-2 py-0.5 rounded-full mb-3"
                                  style="background:{{ $bc }}18;color:{{ $bc }};">
                                {{ strtoupper($ticket->status->label()) }}
                            </span>

                            @if ($ticket->isOverdue())
                                <span class="inline-flex text-[10px] font-bold px-2 py-0.5 rounded-full mb-3 ml-1"
                                      style="background:#fee2e2;color:#b91c1c;">⚠ OVERDUE</span>
                            @endif

                            {{-- Footer: assignee avatar + time + ticket number --}}
                            <div class="flex items-center justify-between mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                                <div class="flex items-center gap-2">
                                    <div class="av" style="background:{{ $sc }};width:26px;height:26px;font-size:10px;">
                                        {{ strtoupper(substr($ticket->user->name,0,1)) }}
                                    </div>
                                    <span class="text-xs" style="color:#94a3b8;">{{ $ticket->updated_at->diffForHumans() }}</span>
                                </div>
                                <span class="text-[10px] font-black rounded px-1.5 py-0.5" style="background:#f1f5f9;color:#64748b;">#{{ $ticket->id }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between px-5 py-3" style="border-top:1px solid #f1f5f9;">
        <p class="text-xs" style="color:#94a3b8;">
            Showing <span class="font-semibold text-gray-700">{{ $tickets->firstItem() ?? 0 }}–{{ $tickets->lastItem() ?? 0 }}</span>
            of <span class="font-semibold text-gray-700">{{ $tickets->total() }}</span> tickets
        </p>
        {{ $tickets->links() }}
    </div>
</div>{{-- end card --}}
</div>{{-- end main col --}}

{{-- ── RIGHT PREVIEW PANEL ───────────────────────────────────────────────── --}}
<aside class="hidden xl:flex xl:flex-col w-72 shrink-0 card sticky top-4 max-h-[calc(100vh-5rem)] overflow-hidden">
    <div class="px-5 py-4" style="border-bottom:1px solid #f1f5f9;">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Preview</p>
        <p class="font-bold text-gray-900 mt-0.5 text-sm">Ticket detail</p>
    </div>

    @if ($previewTicket)
        @php
            $sv = $previewTicket->status->value;
            $bc2 = $statusColor[$sv] ?? '#94a3b8';
            $sc2 = $avatarColors[crc32($previewTicket->user->name) % count($avatarColors)];
            $ac2 = $previewTicket->assignee ? $avatarColors[crc32($previewTicket->assignee->name) % count($avatarColors)] : null;
            $priColors = ['low'=>['bg'=>'#f3f4f6','text'=>'#6b7280'],'medium'=>['bg'=>'#dbeafe','text'=>'#1d4ed8'],'high'=>['bg'=>'#fef3c7','text'=>'#b45309'],'urgent'=>['bg'=>'#fee2e2','text'=>'#b91c1c']];
            $pc2 = $priColors[$previewTicket->priority->value] ?? $priColors['low'];
        @endphp
        <div class="h-1 w-full" style="background:{{ $bc2 }};"></div>
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            {{-- ID + priority + status --}}
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="text-[10px] font-mono font-bold" style="color:#94a3b8;">#{{ $previewTicket->id }}</span>
                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full" style="background:{{ $bc2 }}18;color:{{ $bc2 }};">{{ $previewTicket->status->label() }}</span>
                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full" style="background:{{ $pc2['bg'] }};color:{{ $pc2['text'] }};">{{ $previewTicket->priority->label() }}</span>
                </div>
                <h3 class="text-sm font-bold text-gray-900 leading-snug">{{ $previewTicket->subject }}</h3>
                <p class="mt-1.5 text-xs leading-relaxed line-clamp-3" style="color:#64748b;">{{ $previewTicket->description }}</p>
            </div>

            {{-- Requester --}}
            <div style="border-top:1px solid #f1f5f9;padding-top:12px;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Requester</p>
                <div class="flex items-center gap-2.5 rounded-lg p-2.5" style="background:#f8fafc;">
                    <div class="av" style="background:{{ $sc2 }};width:30px;height:30px;font-size:11px;">{{ strtoupper(substr($previewTicket->user->name,0,1)) }}</div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $previewTicket->user->name }}</p>
                        <p class="text-[10px] truncate" style="color:#94a3b8;">{{ $previewTicket->user->email }}</p>
                    </div>
                </div>
            </div>

            {{-- Agent --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Agent</p>
                    @if ($previewTicket->assigned_to !== auth()->id())
                        <form method="POST" action="{{ route('admin.tickets.update', $previewTicket) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $previewTicket->status->value }}">
                            <input type="hidden" name="priority" value="{{ $previewTicket->priority->value }}">
                            <input type="hidden" name="assigned_to" value="{{ auth()->id() }}">
                            <button type="submit" class="text-[10px] font-bold" style="color:#6366f1;">Assign me</button>
                        </form>
                    @endif
                </div>
                @if ($previewTicket->assignee)
                    <div class="flex items-center gap-2.5 rounded-lg p-2.5" style="background:#f8fafc;">
                        <div class="av" style="background:{{ $ac2 }};width:30px;height:30px;font-size:11px;">{{ strtoupper(substr($previewTicket->assignee->name,0,1)) }}</div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ $previewTicket->assignee->name }}</p>
                            <p class="text-[10px] truncate" style="color:#94a3b8;">{{ $previewTicket->assignee->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-xs font-semibold" style="color:#ef4444;">No agent assigned</p>
                @endif
            </div>

            {{-- Meta --}}
            <dl class="space-y-2 text-xs" style="border-top:1px solid #f1f5f9;padding-top:12px;">
                @foreach ([['Category',$previewTicket->category->name],['Opened',$previewTicket->created_at->format('M j, Y')]] as [$l,$v])
                    <div class="flex justify-between gap-2">
                        <dt style="color:#94a3b8;">{{ $l }}</dt>
                        <dd class="font-medium text-gray-700 text-right truncate">{{ $v }}</dd>
                    </div>
                @endforeach
            </dl>

            {{-- Quick update --}}
            <form method="POST" action="{{ route('admin.tickets.update', $previewTicket) }}"
                  class="space-y-2" style="border-top:1px solid #f1f5f9;padding-top:12px;">
                @csrf @method('PATCH')
                <p class="text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Quick update</p>
                <select name="status" class="input px-3 py-2 text-xs">
                    @foreach (\App\Enums\TicketStatus::cases() as $s)<option value="{{ $s->value }}" @selected($previewTicket->status===$s)>{{ $s->label() }}</option>@endforeach
                </select>
                <select name="priority" class="input px-3 py-2 text-xs">
                    @foreach (\App\Enums\TicketPriority::cases() as $p)<option value="{{ $p->value }}" @selected($previewTicket->priority===$p)>{{ $p->label() }}</option>@endforeach
                </select>
                <select name="assigned_to" class="input px-3 py-2 text-xs">
                    <option value="">Unassigned</option>
                    @foreach ($staff as $m)<option value="{{ $m->id }}" @selected($previewTicket->assigned_to===$m->id)>{{ $m->name }}</option>@endforeach
                </select>
                <button type="submit" class="btn btn-primary w-full justify-center text-xs py-2">Save changes</button>
            </form>

            {{-- Latest replies --}}
            <div style="border-top:1px solid #f1f5f9;padding-top:12px;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Latest replies</p>
                <div class="space-y-2">
                    @forelse ($previewTicket->replies->take(-3) as $reply)
                        <div class="rounded-lg p-3" style="background:#f8fafc;border:1px solid #f1f5f9;">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-gray-800">{{ $reply->user->name }}</span>
                                <span class="text-[10px]" style="color:#94a3b8;">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs line-clamp-2" style="color:#64748b;">{{ $reply->body }}</p>
                        </div>
                    @empty
                        <p class="text-xs italic" style="color:#94a3b8;">No replies yet.</p>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('admin.tickets.show', $previewTicket) }}" class="btn btn-secondary w-full justify-center text-xs py-2">
                Open full ticket →
            </a>
        </div>
    @else
        <div class="flex-1 flex items-center justify-center p-8 text-center">
            <div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3" style="background:#f1f5f9;">
                    <svg class="w-6 h-6" style="color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-600">No ticket selected</p>
                <p class="mt-1 text-xs" style="color:#94a3b8;">Click a row to preview.</p>
            </div>
        </div>
    @endif
</aside>

</div>{{-- end flex gap-4 --}}
@endsection
