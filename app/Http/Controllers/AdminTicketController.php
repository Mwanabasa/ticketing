<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Requests\ReplyRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    public function index(Request $request): View
    {
        $viewCounts = [
            'assigned_to_me' => Ticket::query()
                ->where('assigned_to', $request->user()->id)
                ->whereIn('status', [TicketStatus::Open, TicketStatus::Pending])
                ->count(),
            'all_open' => Ticket::query()->where('status', TicketStatus::Open)->count(),
            'unassigned' => Ticket::query()
                ->whereNull('assigned_to')
                ->whereIn('status', [TicketStatus::Open, TicketStatus::Pending])
                ->count(),
            'pending' => Ticket::query()->where('status', TicketStatus::Pending)->count(),
            'resolved' => Ticket::query()->where('status', TicketStatus::Resolved)->count(),
            'closed' => Ticket::query()->where('status', TicketStatus::Closed)->count(),
        ];

        $query = Ticket::query()
            ->with(['user', 'category', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('assigned_to')) {
            if ((string) $request->input('assigned_to') === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->integer('assigned_to'));
            }
        }

        if ($request->filled('q')) {
            $needle = $request->string('q')->trim();
            $query->where(function ($q) use ($needle): void {
                $q->where('subject', 'like', '%'.$needle.'%')
                    ->orWhere('description', 'like', '%'.$needle.'%');
                if (ctype_digit($needle)) {
                    $q->orWhere('id', (int) $needle);
                }
            });
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();

        $previewTicket = null;
        if ($request->filled('ticket')) {
            $previewTicket = Ticket::query()
                ->with(['user', 'category', 'assignee', 'replies.user'])
                ->find($request->integer('ticket'));
        }

        if (! $previewTicket) {
            $previewTicket = $tickets->getCollection()->first();
            $previewTicket?->load(['replies.user']);
        }

        $staff = User::query()
            ->where('role', UserRole::Staff)
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::query()->orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'staff', 'categories', 'viewCounts', 'previewTicket'));
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('manage', $ticket);

        $ticket->load(['user', 'category', 'assignee', 'replies.user']);

        $staff = User::query()
            ->where('role', UserRole::Staff)
            ->orderBy('name')
            ->get();

        return view('admin.tickets.show', compact('ticket', 'staff'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        if ($request->input('assigned_to') === '' || $request->input('assigned_to') === '0') {
            $request->merge(['assigned_to' => null]);
        }

        $validated = $request->validated();

        if ($validated['assigned_to'] ?? null) {
            $assignee = User::query()->findOrFail($validated['assigned_to']);
            if (! $assignee->isStaff()) {
                return back()->withErrors(['assigned_to' => 'Only support staff can be assigned.']);
            }
        }

        $ticket->update([
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        return back()->with('status', 'Ticket updated.');
    }

    public function reply(ReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        $validated = $request->validated();

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        if ($ticket->status === TicketStatus::Open) {
            $ticket->update(['status' => TicketStatus::Pending]);
        }

        return back()->with('status', 'Reply posted.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_ids' => ['required', 'array'],
            'ticket_ids.*' => ['exists:tickets,id'],
            'action' => ['required', 'in:assign_status,assign_priority,close'],
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
            'priority' => ['nullable', Rule::enum(TicketPriority::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $tickets = Ticket::query()->whereIn('id', $validated['ticket_ids'])->get();

        foreach ($tickets as $ticket) {
            $this->authorize('manage', $ticket);

            match ($validated['action']) {
                'assign_status' => $ticket->update([
                    'status' => $validated['status'] ?? $ticket->status,
                ]),
                'assign_priority' => $ticket->update([
                    'priority' => $validated['priority'] ?? $ticket->priority,
                ]),
                'close' => $ticket->update([
                    'status' => TicketStatus::Closed,
                ]),
            };
        }

        return back()->with('status', count($tickets).' tickets updated.');
    }

    public function merge(Request $request): RedirectResponse
    {
        // Support both a raw comma-separated string (from the UI form) and a pre-built array
        if ($request->filled('merge_ids_raw') && ! $request->has('source_ticket_ids')) {
            $ids = array_filter(array_map('trim', explode(',', $request->input('merge_ids_raw'))));
            $request->merge(['source_ticket_ids' => $ids]);
        }

        $validated = $request->validate([
            'source_ticket_ids' => ['required', 'array', 'min:1'],
            'source_ticket_ids.*' => ['exists:tickets,id'],
            'target_ticket_id' => ['required', 'exists:tickets,id'],
        ]);

        $targetTicket = Ticket::query()->findOrFail($validated['target_ticket_id']);
        $this->authorize('manage', $targetTicket);

        // Filter out the target ticket if accidentally included in source list
        $sourceIds = array_filter(
            $validated['source_ticket_ids'],
            fn ($id) => (int) $id !== (int) $validated['target_ticket_id']
        );

        if (empty($sourceIds)) {
            return back()->withErrors(['merge_ids_raw' => 'No valid source tickets to merge.']);
        }

        $sourceTickets = Ticket::query()->whereIn('id', $sourceIds)->get();

        foreach ($sourceTickets as $sourceTicket) {
            $this->authorize('manage', $sourceTicket);

            // Move replies to target ticket
            $sourceTicket->replies()->update(['ticket_id' => $targetTicket->id]);

            // Move time entries to target ticket
            $sourceTicket->timeEntries()->update(['ticket_id' => $targetTicket->id]);

            // Close source ticket
            $sourceTicket->update([
                'status' => TicketStatus::Closed,
                'description' => $sourceTicket->description."\n\n[Merged into ticket #{$targetTicket->id}]",
            ]);
        }

        return back()->with('status', count($sourceTickets).' tickets merged into #'.$targetTicket->id.'.');
    }
}
