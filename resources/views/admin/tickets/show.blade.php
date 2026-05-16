@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)

@section('content')
    <p class="text-sm text-slate-500">
        <a href="{{ route('admin.tickets.index') }}" class="font-medium text-slate-700 underline">← All tickets</a>
    </p>

    <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">#{{ $ticket->id }} — {{ $ticket->subject }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $ticket->user->name }} ({{ $ticket->user->email }}) · {{ $ticket->category->name }}
            </p>
        </div>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @if ($ticket->attachment_path)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 class="text-sm font-semibold text-slate-700">Student attachment</h2>
                    <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" rel="noopener" class="mt-2 inline-block">
                        <img src="{{ asset('storage/'.$ticket->attachment_path) }}" alt="Attachment" class="max-h-72 max-w-full rounded-lg border object-contain">
                    </a>
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Description</h2>
                <div class="prose prose-sm mt-3 max-w-none whitespace-pre-wrap text-slate-800">{{ $ticket->description }}</div>
            </div>

            <section>
                <h2 class="text-lg font-semibold text-slate-900">Thread</h2>
                <ul class="mt-4 space-y-4">
                    @foreach ($ticket->replies as $reply)
                        <li class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex flex-wrap justify-between gap-2 text-sm">
                                <span class="font-medium text-slate-900">{{ $reply->user->name }}</span>
                                <span class="text-xs text-slate-500">{{ $reply->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if ($reply->user->isStaff())
                                <span class="mt-1 inline-block rounded bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">Staff</span>
                            @else
                                <span class="mt-1 inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">Student</span>
                            @endif
                            <p class="mt-3 whitespace-pre-wrap text-sm text-slate-700">{{ $reply->body }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>

            <form method="POST" action="{{ route('admin.tickets.replies.store', $ticket) }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                <h2 class="text-sm font-semibold text-slate-900">Reply to student</h2>
                <textarea name="body" rows="4" required class="mt-3 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" placeholder="Your message…">{{ old('body') }}</textarea>
                <button type="submit" class="mt-4 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Send reply</button>
            </form>
        </div>

        <aside class="space-y-6">
            <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PATCH')
                <h2 class="text-sm font-semibold text-slate-900">Manage ticket</h2>

                <div class="mt-4">
                    <label for="status" class="block text-xs font-medium uppercase text-slate-500">Status</label>
                    <select id="status" name="status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        @foreach (\App\Enums\TicketStatus::cases() as $s)
                            <option value="{{ $s->value }}" @selected(old('status', $ticket->status->value) === $s->value)>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <label for="assigned_to" class="block text-xs font-medium uppercase text-slate-500">Assign to</label>
                    <select id="assigned_to" name="assigned_to" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Unassigned</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}" @selected(old('assigned_to', $ticket->assigned_to) == $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="mt-6 w-full rounded-lg bg-slate-900 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save changes</button>
            </form>
        </aside>
    </div>
@endsection
