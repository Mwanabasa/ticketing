@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)
@section('page_title', 'Ticket #'.$ticket->id)
@section('page_subtitle', $ticket->subject)

@section('content')

    <div class="mb-5">
        <a href="{{ route('student.tickets.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            My tickets
        </a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- LEFT: thread --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Header --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-lg font-bold text-slate-900 leading-snug">{{ $ticket->subject }}</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $ticket->category->name }} · Opened {{ $ticket->created_at->format('M j, Y g:i A') }}
                        </p>
                        @if ($ticket->assignee)
                            <div class="mt-2 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($ticket->assignee->name, 0, 1)) }}
                                </div>
                                <span class="text-sm text-slate-600">Assigned to <span class="font-semibold text-slate-800">{{ $ticket->assignee->name }}</span></span>
                            </div>
                        @else
                            <p class="mt-2 text-sm text-slate-400">Not yet assigned to staff</p>
                        @endif
                        @if ($ticket->due_at && !in_array($ticket->status, [\App\Enums\TicketStatus::Resolved, \App\Enums\TicketStatus::Closed]))
                            <p class="mt-2 flex items-center gap-1.5 text-xs {{ $ticket->isOverdue() ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $ticket->isOverdue() ? 'Overdue — due ' : 'Due ' }}{{ $ticket->due_at->format('M j, Y g:i A') }}
                            </p>
                        @endif
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
                             class="max-h-64 max-w-full rounded-xl border border-slate-200 object-contain shadow-sm">
                    </a>
                </div>
            @endif

            {{-- Original message --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Your original message</p>
                <p class="whitespace-pre-wrap text-sm text-slate-700 leading-relaxed">{{ $ticket->description }}</p>
            </div>

            {{-- Conversation --}}
            @if ($ticket->replies->isNotEmpty())
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 px-1 mb-4">
                        Conversation · {{ $ticket->replies->count() }} {{ Str::plural('reply', $ticket->replies->count()) }}
                    </p>
                    <div class="relative space-y-4">
                        {{-- Timeline line --}}
                        <div class="absolute left-4 top-4 bottom-4 w-px bg-slate-100"></div>

                        @foreach ($ticket->replies as $reply)
                            @php
                                $isStaff = $reply->user->isStaff();
                                $avatarBg = $isStaff ? 'linear-gradient(135deg,#4f46e5,#7c3aed)' : 'linear-gradient(135deg,#64748b,#94a3b8)';
                            @endphp
                            <div class="relative flex gap-4">
                                {{-- Avatar on timeline --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0 z-10 ring-2 ring-white"
                                     style="background: {{ $avatarBg }};">
                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                </div>

                                <div class="flex-1 rounded-2xl border p-4 shadow-sm
                                    {{ $isStaff ? 'bg-indigo-50 border-indigo-100' : 'bg-white border-slate-200' }}">
                                    <div class="flex items-center justify-between gap-2 mb-2.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-slate-900">{{ $reply->user->name }}</span>
                                            @if ($isStaff)
                                                <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-700 uppercase tracking-wide">Staff</span>
                                            @endif
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
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Reply form --}}
            @can('replyAsStudent', $ticket)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <p class="text-sm font-semibold text-slate-900 mb-3">Add a reply</p>
                    <form method="POST" action="{{ route('student.tickets.replies.store', $ticket) }}"
                          enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <textarea name="body" rows="4" required placeholder="Add details or answer staff questions…"
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
                @if ($ticket->status === \App\Enums\TicketStatus::Closed)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center text-sm text-slate-400">
                        This ticket is closed. Please open a new ticket if you need further help.
                    </div>
                @endif
            @endcan

            {{-- Rating --}}
            @if (in_array($ticket->status, [\App\Enums\TicketStatus::Resolved, \App\Enums\TicketStatus::Closed]))
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    @if ($ticket->rating)
                        <p class="text-sm font-semibold text-slate-900 mb-2">Your rating</p>
                        <div class="flex items-center gap-1 text-amber-400 text-2xl mb-1">
                            @for ($i = 1; $i <= 5; $i++){{ $i <= $ticket->rating ? '★' : '☆' }}@endfor
                        </div>
                        @if ($ticket->rating_comment)
                            <p class="text-sm text-slate-500 italic">"{{ $ticket->rating_comment }}"</p>
                        @endif
                    @else
                        <p class="text-sm font-semibold text-slate-900 mb-1">How was your experience?</p>
                        <p class="text-xs text-slate-400 mb-4">Your feedback helps us improve our support.</p>
                        <form method="POST" action="{{ route('student.tickets.rate', $ticket) }}" class="space-y-3">
                            @csrf
                            <div class="flex items-center gap-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                                        <span class="text-3xl text-slate-200 peer-checked:text-amber-400 group-hover:text-amber-300 transition select-none">★</span>
                                    </label>
                                @endfor
                            </div>
                            <textarea name="rating_comment" rows="2" placeholder="Optional comment…"
                                      class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 resize-none"></textarea>
                            <button type="submit" class="rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition">
                                Submit rating
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        {{-- RIGHT: sidebar --}}
        <aside class="space-y-4">

            {{-- Ticket info --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Ticket info</p>
                <dl class="space-y-2.5 text-sm">
                    @foreach ([
                        ['Category',     $ticket->category->name],
                        ['Priority',     $ticket->priority->label()],
                        ['Status',       $ticket->status->label()],
                        ['Assignee',     $ticket->assignee?->name ?? 'Not assigned'],
                        ['Replies',      $ticket->replies->count()],
                        ['Opened',       $ticket->created_at->format('M j, Y')],
                        ['Last updated', $ticket->updated_at->diffForHumans()],
                    ] as [$label, $value])
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-400 shrink-0">{{ $label }}</dt>
                            <dd class="font-medium text-slate-800 text-right">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- KB suggestions --}}
            @if ($suggestions->isNotEmpty())
                <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-indigo-600 mb-3">Related articles</p>
                    <div class="space-y-2">
                        @foreach ($suggestions as $article)
                            <a href="{{ route('knowledge-base.show', $article) }}"
                               class="flex items-center justify-between rounded-xl bg-white border border-indigo-100 px-3 py-2.5 text-sm hover:border-indigo-300 hover:shadow-sm transition group">
                                <span class="font-medium text-slate-800 text-xs">{{ $article->title }}</span>
                                <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- New ticket CTA --}}
            <a href="{{ route('student.tickets.create') }}"
               class="flex items-center gap-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:bg-slate-50 transition group">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800">Open new ticket</p>
                    <p class="text-xs text-slate-400">Have a different issue?</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </aside>
    </div>
@endsection
