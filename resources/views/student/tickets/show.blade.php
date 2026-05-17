@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)
@section('page_title', 'Ticket #'.$ticket->id)

@section('content')
    <div class="mb-4">
        <a href="{{ route('student.tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">← My tickets</a>
    </div>

    {{-- Header card --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $ticket->subject }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $ticket->category->name }} · Opened {{ $ticket->created_at->format('M j, Y g:i A') }}
                </p>
                @if ($ticket->assignee)
                    <p class="mt-2 flex items-center gap-1.5 text-sm text-slate-600">
                        <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">
                            {{ strtoupper(substr($ticket->assignee->name, 0, 1)) }}
                        </span>
                        Assigned to <span class="font-semibold text-slate-800">{{ $ticket->assignee->name }}</span>
                    </p>
                @else
                    <p class="mt-2 text-sm text-slate-400">Not yet assigned to staff</p>
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
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Attachment</p>
            <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" rel="noopener">
                <img src="{{ asset('storage/'.$ticket->attachment_path) }}" alt="Attachment"
                     class="max-h-64 max-w-full rounded-xl border border-slate-200 object-contain shadow-sm">
            </a>
        </div>
    @endif

    {{-- Original description --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Your original message</p>
        <p class="whitespace-pre-wrap text-sm text-slate-800 leading-relaxed">{{ $ticket->description }}</p>
    </div>

    {{-- Conversation --}}
    @if ($ticket->replies->isNotEmpty())
        <div class="space-y-3 mb-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Conversation ({{ $ticket->replies->count() }})</p>
            @foreach ($ticket->replies as $reply)
                <div class="rounded-2xl border p-4 shadow-sm {{ $reply->user->isStaff() ? 'border-indigo-100 bg-indigo-50' : 'border-slate-200 bg-white' }}">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $reply->user->isStaff() ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">
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
    @can('replyAsStudent', $ticket)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-900 mb-3">Add a reply</p>
            <form method="POST" action="{{ route('student.tickets.replies.store', $ticket) }}" class="space-y-3">
                @csrf
                <textarea id="body" name="body" rows="4" required placeholder="Add details or answer staff questions…"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('body') }}</textarea>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Send reply
                </button>
            </form>
        </div>
    @else
        @if ($ticket->status === \App\Enums\TicketStatus::Closed)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center text-sm text-slate-400">
                This ticket is closed. Please open a new ticket if you need further help.
            </div>
        @endif
    @endcan
@endsection
