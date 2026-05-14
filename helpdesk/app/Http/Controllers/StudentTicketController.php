<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentTicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->where('user_id', $request->user()->id)
            ->with(['category', 'assignee'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('student.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('student.tickets.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'attachment' => ['nullable', 'image', 'max:4096'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::query()->create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => TicketStatus::Open,
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()
            ->route('student.tickets.show', $ticket)
            ->with('status', 'Your support ticket has been submitted.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['category', 'assignee', 'replies.user']);

        return view('student.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('replyAsStudent', $ticket);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        if ($ticket->status === TicketStatus::Resolved) {
            $ticket->update(['status' => TicketStatus::Open]);
        }

        return back()->with('status', 'Your message was posted.');
    }
}
