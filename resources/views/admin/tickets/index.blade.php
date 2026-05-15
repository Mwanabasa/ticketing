@extends('layouts.app')

@section('title', 'All tickets')
@section('page_title', 'Help desk inbox')

@section('content')
    @php
        $statusTabs = [
            ['label' => 'All', 'value' => null],
            ['label' => 'Open', 'value' => 'open'],
            ['label' => 'Pending', 'value' => 'pending'],
            ['label' => 'Resolved', 'value' => 'resolved'],
            ['label' => 'Closed', 'value' => 'closed'],
        ];

        $inboxViews = [
            ['label' => 'Assigned to me', 'count' => $viewCounts['assigned_to_me'], 'params' => ['assigned_to' => auth()->id(), 'status' => null]],
            ['label' => 'All open', 'count' => $viewCounts['all_open'], 'params' => ['status' => 'open', 'assigned_to' => null]],
            ['label' => 'Unassigned', 'count' => $viewCounts['unassigned'], 'params' => ['assigned_to' => 'unassigned', 'status' => null]],
            ['label' => 'Pending', 'count' => $viewCounts['pending'], 'params' => ['status' => 'pending', 'assigned_to' => null]],
            ['label' => 'Resolved', 'count' => $viewCounts['resolved'], 'params' => ['status' => 'resolved', 'assigned_to' => null]],
            ['label' => 'Closed', 'count' => $viewCounts['closed'], 'params' => ['status' => 'closed', 'assigned_to' => null]],
        ];
    @endphp

    <div class="grid min-h-[calc(100vh-12rem)] gap-4 xl:grid-cols-[17rem_minmax(0,1fr)_22rem]">
        <aside class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Help desk</p>
                        <h1 class="mt-1 text-xl font-bold text-slate-950">Ticket views</h1>
                    </div>
                    <span class="rounded-lg bg-rose-600 px-2.5 py-1 text-sm font-bold text-white">{{ $tickets->total() }}</span>
                </div>
            </div>

            <nav class="space-y-1 p-3">
                @foreach ($inboxViews as $view)
                    @php
                        $params = request()->except('page', 'ticket');
                        foreach ($view['params'] as $key => $value) {
                            if ($value === null) {
                                unset($params[$key]);
                            } else {
                                $params[$key] = $value;
                            }
                        }

                        $isActive = true;
                        foreach ($view['params'] as $key => $value) {
                            if ($value === null) {
                                $isActive = $isActive && ! request()->filled($key);
                            } else {
                                $isActive = $isActive && (string) request($key) === (string) $value;
                            }
                        }
                    @endphp
                    <a href="{{ route('admin.tickets.index', $params) }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold {{ $isActive ? 'bg-cyan-50 text-cyan-800 ring-1 ring-cyan-200' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>{{ $view['label'] }}</span>
                        <span class="text-xs font-bold {{ $isActive ? 'text-cyan-700' : 'text-slate-400' }}">{{ $view['count'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-200 p-4">
                <a href="{{ route('admin.tickets.index') }}" class="flex w-full items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Restore all tickets
                </a>
            </div>
        </aside>

        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-bold text-slate-950">Tickets</h2>
                            <span class="rounded-lg bg-rose-600 px-2.5 py-1 text-sm font-bold text-white">{{ $tickets->total() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Select a ticket to preview details, or update status and assignment inline.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($statusTabs as $tab)
                            @php
                                $isActive = $tab['value'] === null
                                    ? ! request()->filled('status')
                                    : request('status') === $tab['value'];
                                $url = $tab['value'] === null
                                    ? route('admin.tickets.index', request()->except('status', 'page', 'ticket'))
                                    : route('admin.tickets.index', array_merge(request()->except('page', 'ticket'), ['status' => $tab['value']]));
                            @endphp
                            <a href="{{ $url }}" class="rounded-xl px-3 py-2 text-sm font-semibold shadow-sm {{ $isActive ? 'bg-rose-600 text-white' : 'bg-slate-50 text-slate-700 ring-1 ring-slate-200 hover:bg-white' }}">
                                {{ $tab['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.tickets.index') }}" class="mt-4 grid gap-3 lg:grid-cols-[minmax(0,1fr)_11rem_11rem_11rem_auto]">
                    <input name="q" type="search" value="{{ request('q') }}" placeholder="Search subject, description, or ID"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <select name="status" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <option value="">Any status</option>
                        @foreach (\App\Enums\TicketStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <select name="category_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <option value="">Any category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="assigned_to" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <option value="">Any assignee</option>
                        <option value="unassigned" @selected(request('assigned_to') === 'unassigned')>Unassigned</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}" @selected((string) request('assigned_to') === (string) $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Filter</button>
                </form>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($tickets as $ticket)
                    @php
                        $isSelected = $previewTicket?->id === $ticket->id;
                        $previewParams = array_merge(request()->except('page'), ['ticket' => $ticket->id]);
                    @endphp
                    <article class="grid gap-4 p-4 hover:bg-slate-50/80 {{ $isSelected ? 'bg-cyan-50/70 ring-1 ring-inset ring-cyan-200' : '' }} lg:grid-cols-[minmax(0,1fr)_22rem]">
                        <a href="{{ route('admin.tickets.index', $previewParams) }}" class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-slate-950">#{{ $ticket->id }}</span>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $ticket->status->badgeClass() }}">{{ $ticket->status->label() }}</span>
                                <span class="text-xs font-medium text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="mt-2 truncate text-base font-semibold text-slate-950">{{ $ticket->subject }}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $ticket->description }}</p>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs font-medium text-slate-500">
                                <span class="rounded-lg bg-slate-100 px-2 py-1">{{ $ticket->user->name }}</span>
                                <span class="rounded-lg bg-slate-100 px-2 py-1">{{ $ticket->category->name }}</span>
                                <span class="rounded-lg bg-slate-100 px-2 py-1">{{ $ticket->assignee?->name ?? 'Unassigned' }}</span>
                            </div>
                        </a>

                        <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="grid content-start gap-2 sm:grid-cols-[1fr_1fr_auto] lg:grid-cols-1 xl:grid-cols-[1fr_1fr_auto]">
                            @csrf
                            @method('PATCH')
                            <label class="sr-only" for="status-{{ $ticket->id }}">Status</label>
                            <select id="status-{{ $ticket->id }}" name="status" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                                @foreach (\App\Enums\TicketStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>

                            <label class="sr-only" for="assigned-to-{{ $ticket->id }}">Assignee</label>
                            <select id="assigned-to-{{ $ticket->id }}" name="assigned_to" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                                <option value="">Unassigned</option>
                                @foreach ($staff as $member)
                                    <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Save</button>
                        </form>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="font-medium text-slate-700">No tickets match your filters.</p>
                        <p class="mt-1 text-sm text-slate-500">Try clearing filters or searching a different term.</p>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-slate-200 p-4">{{ $tickets->links() }}</div>
        </section>

        <aside class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Quick preview</p>
                <h2 class="mt-1 text-xl font-bold text-slate-950">Ticket detail</h2>
            </div>

            @if ($previewTicket)
                <div class="space-y-5 p-5">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-slate-950">#{{ $previewTicket->id }}</span>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $previewTicket->status->badgeClass() }}">{{ $previewTicket->status->label() }}</span>
                        </div>
                        <h3 class="mt-3 text-lg font-bold leading-6 text-slate-950">{{ $previewTicket->subject }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ Str::limit($previewTicket->description, 220) }}</p>
                    </div>

                    <dl class="grid gap-3 text-sm">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Student</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $previewTicket->user->name }}</dd>
                            <dd class="text-xs text-slate-500">{{ $previewTicket->user->email }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Category</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $previewTicket->category->name }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Assignee</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $previewTicket->assignee?->name ?? 'Unassigned' }}</dd>
                        </div>
                    </dl>

                    <form method="POST" action="{{ route('admin.tickets.update', $previewTicket) }}" class="rounded-xl border border-slate-200 p-3">
                        @csrf
                        @method('PATCH')
                        <h3 class="text-sm font-bold text-slate-950">Quick update</h3>
                        <div class="mt-3 space-y-2">
                            <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                                @foreach (\App\Enums\TicketStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($previewTicket->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            <select name="assigned_to" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                                <option value="">Unassigned</option>
                                @foreach ($staff as $member)
                                    <option value="{{ $member->id }}" @selected($previewTicket->assigned_to === $member->id)>{{ $member->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Save changes</button>
                        </div>
                    </form>

                    <section>
                        <h3 class="text-sm font-bold text-slate-950">Latest conversation</h3>
                        <div class="mt-3 space-y-3">
                            @forelse ($previewTicket->replies->take(-3) as $reply)
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-sm font-semibold text-slate-800">{{ $reply->user->name }}</span>
                                        <span class="text-xs text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-2 text-sm leading-5 text-slate-600">{{ Str::limit($reply->body, 140) }}</p>
                                </div>
                            @empty
                                <p class="rounded-xl bg-slate-50 p-3 text-sm text-slate-500">No replies yet.</p>
                            @endforelse
                        </div>
                    </section>

                    <a href="{{ route('admin.tickets.show', $previewTicket) }}" class="flex w-full items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Open full ticket
                    </a>
                </div>
            @else
                <div class="p-8 text-center text-sm text-slate-500">Select a ticket to preview details.</div>
            @endif
        </aside>
    </div>
@endsection
