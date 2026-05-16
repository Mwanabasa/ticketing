@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Reports</h1>
    <p class="mt-1 text-slate-600">Summaries by status and category. Filter by ticket creation date.</p>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-8 flex flex-wrap items-end gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div>
            <label for="from" class="block text-xs font-medium uppercase text-slate-500">From</label>
            <input id="from" name="from" type="date" value="{{ optional($from)->format('Y-m-d') }}"
                class="mt-1 rounded-md border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="to" class="block text-xs font-medium uppercase text-slate-500">To</label>
            <input id="to" name="to" type="date" value="{{ optional($to)->format('Y-m-d') }}"
                class="mt-1 rounded-md border border-slate-300 px-3 py-2 text-sm">
        </div>
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Update report</button>
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-600 underline">Clear dates</a>
    </form>

    <p class="mt-6 text-sm text-slate-600">Total tickets in range: <span class="font-semibold text-slate-900">{{ $totalTickets }}</span></p>

    <div class="mt-10 grid gap-8 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">By status</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @forelse ($byStatus as $status => $count)
                    <li class="flex justify-between py-2 text-sm">
                        <span class="text-slate-700">{{ \App\Enums\TicketStatus::tryFrom($status)?->label() ?? $status }}</span>
                        <span class="font-medium text-slate-900">{{ $count }}</span>
                    </li>
                @empty
                    <li class="py-4 text-slate-500">No data.</li>
                @endforelse
            </ul>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">By category</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @forelse ($byCategory as $name => $count)
                    <li class="flex justify-between py-2 text-sm">
                        <span class="text-slate-700">{{ $name }}</span>
                        <span class="font-medium text-slate-900">{{ $count }}</span>
                    </li>
                @empty
                    <li class="py-4 text-slate-500">No data.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <section class="mt-12">
        <h2 class="text-lg font-semibold text-slate-900">Recently resolved (sample)</h2>
        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Ticket</th>
                        <th class="px-4 py-3">Student</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Resolved</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentResolved as $t)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.tickets.show', $t) }}" class="font-medium text-slate-900 hover:underline">#{{ $t->id }}</a>
                                <div class="text-xs text-slate-500">{{ Str::limit($t->subject, 36) }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $t->user->name }}</td>
                            <td class="px-4 py-3">{{ $t->category->name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $t->updated_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No resolved tickets in this range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
