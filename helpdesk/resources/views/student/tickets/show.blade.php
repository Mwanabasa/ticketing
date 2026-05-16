@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">Ticket #{{ $ticket->id }}</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $ticket->subject }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $ticket->category->name }}
                · Created {{ $ticket->created_at->format('M j, Y g:i A') }}
            </p>
        </div>
        <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium {{ $ticket->status->badgeClass() }}">
            {{ $ticket->status->label() }}
        </span>
    </div>

    @if ($ticket->assignee)
        <p class="mt-4 text-sm text-slate-600">Assigned to <span class="font-medium text-slate-900">{{ $ticket->assignee->name }}</span></p>
    @endif

    @if ($ticket->attachment_path)
        <div class="mt-6">
            <h2 class="text-sm font-semibold text-slate-700">Attachment</h2>
            <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" rel="noopener" class="mt-2 inline-block">
                <img src="{{ asset('storage/'.$ticket->attachment_path) }}" alt="Ticket attachment" class="max-h-64 max-w-full rounded-lg border border-slate-200 object-contain shadow-sm">
            </a>
        </div>
    @endif

    <div class="mt-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Original message</h2>
        <div class="prose prose-sm mt-3 max-w-none whitespace-pre-wrap text-slate-800">{{ $ticket->description }}</div>
    </div>

    <section class="mt-10">
        <h2 class="text-lg font-semibold text-slate-900">Conversation</h2>
        <ul class="mt-4 space-y-4">
            @foreach ($ticket->replies as $reply)
                <li class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                        <span class="font-medium text-slate-900">{{ $reply->user->name }}</span>
                        <span class="text-xs text-slate-500">{{ $reply->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @if ($reply->user->isStaff())
                        <span class="mt-1 inline-block rounded bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">Staff</span>
                    @endif
                    <p class="mt-3 whitespace-pre-wrap text-sm text-slate-700">{{ $reply->body }}</p>
                </li>
            @endforeach
        </ul>
    </section>

    @can('replyAsStudent', $ticket)
        <form method="POST" action="{{ route('student.tickets.replies.store', $ticket) }}" class="mt-8 max-w-xl space-y-4">
            @csrf
            <div>
                <label for="body" class="block text-sm font-medium text-slate-700">Your reply</label>
                <textarea id="body" name="body" rows="4" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="Add details or answer staff questions…">{{ old('body') }}</textarea>
            </div>
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Send reply</button>
        </form>
    @else
        @if ($ticket->status === \App\Enums\TicketStatus::Closed)
            <p class="mt-8 text-sm text-slate-600">This ticket is closed. You cannot add new replies.</p>
        @endif
    @endcan

    <p class="mt-10">
        <a href="{{ route('student.tickets.index') }}" class="text-sm font-medium text-slate-700 underline">← Back to my tickets</a>
    </p>
@endsection
