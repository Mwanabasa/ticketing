@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)
@section('page_title', 'Ticket #'.$ticket->id)
@section('page_subtitle', $ticket->subject)

@section('content')

    {{-- Back + actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <a href="{{ route('admin.tickets.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            All tickets
        </a>
        <div class="flex flex-wrap gap-2">
            @if ($ticket->status !== \App\Enums\TicketStatus::Resolved)
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="resolved">
                    <input type="hidden" name="priority" value="{{ $ticket->priority->value }}">
                    <input type="hidden" name="assigned_to" value="{{ $ticket->assigned_to ?? '' }}">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark resolved
                    </button>
                </form>
            @endif
            @if ($ticket->status !== \App\Enums\TicketStatus::Closed)
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}"
                      onsubmit="return confirm('Close this ticket?')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="closed">
                    <input type="hidden" name="priority" value="{{ $ticket->priority->value }}">
                    <input type="hidden" name="assigned_to" value="{{ $ticket->assigned_to ?? '' }}">
                    <button type="submit" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
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
                    <button type="submit" class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                        Assign to me
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- LEFT: thread --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Header card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-lg font-bold text-slate-900 leading-snug">{{ $ticket->subject }}</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            <span class="font-medium text-slate-700">{{ $ticket->user->name }}</span>
                            · {{ $ticket->user->email }}
                            · {{ $ticket->category->name }}
                            · <span class="text-slate-400">{{ $ticket->created_at->diffForHumans() }}</span>
                        </p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $ticket->priority->badgeClass() }}">{{ $ticket->priority->label() }}</span>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $ticket->status->badgeClass() }}">{{ $ticket->status->label() }}</span>
                    </div>
                </div>
            </div>

            {{-- Attachment --}}
            @if ($ticket->attachment_path)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Attachment</p>
                    <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" rel="noopener">
                        <img src="{{ asset('storage/'.$ticket->attachment_path) }}" alt="Attachment"
                             class="max-h-64 max-w-full rounded-xl border border-slate-200 object-contain">
                    </a>
                </div>
            @endif

            {{-- Original message --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Original message</p>
                <div class="whitespace-pre-wrap text-sm text-slate-700 leading-relaxed">{{ $ticket->description }}</div>
            </div>

            {{-- Conversation --}}
            @if ($ticket->replies->isNotEmpty())
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 px-1">
                        Conversation · {{ $ticket->replies->count() }} {{ Str::plural('reply', $ticket->replies->count()) }}
                    </p>
                    @foreach ($ticket->replies as $reply)
                        <div class="rounded-2xl border p-4 shadow-sm
                            {{ $reply->user->isStaff() ? 'bg-indigo-50 border-indigo-100' : 'bg-white border-slate-200' }}">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                        {{ $reply->user->isStaff() ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-slate-900">{{ $reply->user->name }}</span>
                                        @if ($reply->user->isStaff())
                                            <span class="ml-1.5 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">Staff</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400">{{ $reply->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            <p class="whitespace-pre-wrap text-sm text-slate-700 leading-relaxed">{{ $reply->body }}</p>
                            @if ($reply->attachment_path)
                                <div class="mt-3">
                                    <a href="{{ asset('storage/'.$reply->attachment_path) }}" target="_blank" rel="noopener">
                                        <img src="{{ asset('storage/'.$reply->attachment_path) }}" alt="Attachment"
                                             class="max-h-48 max-w-full rounded-xl border border-slate-200 object-contain">
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Reply form --}}
            @if ($ticket->status !== \App\Enums\TicketStatus::Closed)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <p class="text-sm font-semibold text-slate-900 mb-3">Reply to student</p>
                    <form method="POST" action="{{ route('admin.tickets.replies.store', $ticket) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <textarea name="body" rows="4" required placeholder="Type your reply…"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition resize-none">{{ old('body') }}</textarea>
                        <div class="flex items-center justify-between gap-3">
                            <input type="file" name="attachment" accept="image/*,application/pdf"
                                   class="text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200 transition">
                            <button type="submit" class="shrink-0 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
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

        {{-- RIGHT: sidebar --}}
        <aside class="space-y-4">

            {{-- Manage --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Manage ticket</p>
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    @foreach ([
                        ['id' => 'status',      'label' => 'Status'],
                        ['id' => 'priority',    'label' => 'Priority'],
                        ['id' => 'assigned_to', 'label' => 'Assigned to'],
                    ] as $field)
                        <div>
                            <label for="{{ $field['id'] }}" class="block text-xs font-medium text-slate-500 mb-1">{{ $field['label'] }}</label>
                            @if ($field['id'] === 'status')
                                <select id="status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                                    @foreach (\App\Enums\TicketStatus::cases() as $s)
                                        <option value="{{ $s->value }}" @selected(old('status', $ticket->status->value) === $s->value)>{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                            @elseif ($field['id'] === 'priority')
                                <select id="priority" name="priority" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                                    @foreach (\App\Enums\TicketPriority::cases() as $p)
                                        <option value="{{ $p->value }}" @selected(old('priority', $ticket->priority->value) === $p->value)>{{ $p->label() }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select id="assigned_to" name="assigned_to" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                                    <option value="">Unassigned</option>
                                    @foreach ($staff as $member)
                                        <option value="{{ $member->id }}" @selected(old('assigned_to', $ticket->assigned_to) == $member->id)>{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    @endforeach
                    <div>
                        <label for="due_at" class="block text-xs font-medium text-slate-500 mb-1">SLA due date <span class="text-slate-300">(optional)</span></label>
                        <input type="datetime-local" id="due_at" name="due_at"
                               value="{{ old('due_at', $ticket->due_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Save changes
                    </button>
                </form>
            </div>

            {{-- Info --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Ticket info</p>
                <dl class="space-y-2.5 text-sm">
                    @foreach ([
                        ['Student',      $ticket->user->name],
                        ['Email',        $ticket->user->email],
                        ['Category',     $ticket->category->name],
                        ['Assignee',     $ticket->assignee?->name ?? 'Unassigned'],
                        ['Replies',      $ticket->replies->count()],
                        ['Opened',       $ticket->created_at->format('M j, Y')],
                        ['Last updated', $ticket->updated_at->diffForHumans()],
                    ] as [$label, $value])
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-400 shrink-0">{{ $label }}</dt>
                            <dd class="font-medium text-slate-800 text-right truncate">{{ $value }}</dd>
                        </div>
                    @endforeach
                    @if ($ticket->due_at)
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-400 shrink-0">SLA due</dt>
                            <dd class="font-medium text-right {{ $ticket->isOverdue() ? 'text-red-600' : 'text-slate-800' }}">
                                {{ $ticket->due_at->format('M j, Y g:i A') }}
                                @if ($ticket->isOverdue()) <span class="text-xs">(overdue)</span> @endif
                            </dd>
                        </div>
                    @endif
                    @if ($ticket->rating)
                        <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                            <dt class="text-slate-400">Rating</dt>
                            <dd class="flex items-center gap-0.5 text-amber-400">
                                @for ($i = 1; $i <= 5; $i++){{ $i <= $ticket->rating ? '★' : '☆' }}@endfor
                            </dd>
                        </div>
                        @if ($ticket->rating_comment)
                            <p class="text-xs text-slate-400 italic">"{{ $ticket->rating_comment }}"</p>
                        @endif
                    @endif
                </dl>
            </div>

            {{-- Time entries --}}
            <a href="{{ route('admin.time-entries.index', $ticket) }}"
               class="flex items-center gap-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:bg-slate-50 transition group">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800">Time entries</p>
                    <p class="text-xs text-slate-400">{{ $ticket->totalTimeSpent() }} min logged</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            {{-- Merge --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Merge tickets</p>
                <p class="text-xs text-slate-400 mb-3">Enter comma-separated IDs to merge into this ticket. Source tickets will be closed.</p>
                <form method="POST" action="{{ route('admin.tickets.merge') }}"
                      onsubmit="return confirm('Merge into #{{ $ticket->id }}?')">
                    @csrf
                    <input type="hidden" name="target_ticket_id" value="{{ $ticket->id }}">
                    <input type="text" name="merge_ids_raw" placeholder="e.g. 12, 15, 20"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 mb-2">
                    <button type="submit" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                        Merge into this ticket
                    </button>
                </form>
            </div>
        </aside>
    </div>
@endsection
