<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\ReplyRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentTicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::query()
            ->where('user_id', $request->user()->id)
            ->with(['category', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
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

        $categories = Category::query()->orderBy('name')->get();

        return view('student.tickets.index', compact('tickets', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();
        $templates = TicketTemplate::query()->where('is_active', true)->with('category')->orderBy('name')->get();

        return view('student.tickets.create', compact('categories', 'templates'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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
            'priority' => $validated['priority'],
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

    public function reply(ReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('replyAsStudent', $ticket);

        $validated = $request->validated();

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
