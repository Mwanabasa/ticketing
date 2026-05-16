@extends('layouts.app')

@section('title', 'My tickets')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">My tickets</h1>
        <a href="{{ route('student.tickets.create') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            New ticket
        </a>
    </div>

    @if ($tickets->isEmpty())
        <p class="mt-8 text-slate-600">You have no tickets yet.</p>
    @else
        <div class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Ticket</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($tickets as $ticket)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <a href="{{ route('student.tickets.show', $ticket) }}" class="font-medium text-slate-900 hover:underline">
                                    #{{ $ticket->id }} — {{ Str::limit($ticket->subject, 48) }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $ticket->category->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->status->badgeClass() }}">
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $tickets->links() }}</div>
    @endif
@endsection
