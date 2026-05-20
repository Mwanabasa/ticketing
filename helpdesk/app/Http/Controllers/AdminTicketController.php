<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
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
        $query = Ticket::query()
            ->with(['user', 'category', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('assigned_to')) {
            if ($request->string('assigned_to') === 'unassigned') {
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

        $staff = User::query()
            ->where('role', UserRole::Staff)
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::query()->orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'staff', 'categories'));
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

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        if ($request->input('assigned_to') === '' || $request->input('assigned_to') === '0') {
            $request->merge(['assigned_to' => null]);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::enum(TicketStatus::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        if ($validated['assigned_to'] ?? null) {
            $assignee = User::query()->findOrFail($validated['assigned_to']);
            if (! $assignee->isStaff()) {
                return back()->withErrors(['assigned_to' => 'Only support staff can be assigned.']);
            }
        }

        $ticket->update([
            'status' => $validated['status'],
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        return back()->with('status', 'Ticket updated.');
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        if ($ticket->status === TicketStatus::Open) {
            $ticket->update(['status' => TicketStatus::Pending]);
        }

        return back()->with('status', 'Reply posted.');
    }

    public function resolve(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        $ticket->update(['status' => TicketStatus::Resolved]);

        return back()->with('status', 'Ticket marked as resolved.');
    }
}
