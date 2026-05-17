<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TimeEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeEntryController extends Controller
{
    public function index(Request $request, Ticket $ticket): View
    {
        $this->authorize('manage', $ticket);

        $timeEntries = $ticket->timeEntries()
            ->with('user')
            ->latest('started_at')
            ->paginate(20);

        return view('admin.time-entries.index', compact('ticket', 'timeEntries'));
    }

    public function create(Request $request, Ticket $ticket): View
    {
        $this->authorize('manage', $ticket);

        return view('admin.time-entries.create', compact('ticket'));
    }

    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:1000'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'started_at' => ['nullable', 'date'],
            'stopped_at' => ['nullable', 'date', 'after:started_at'],
        ]);

        $ticket->timeEntries()->create([
            'user_id' => $request->user()->id,
            'description' => $validated['description'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'started_at' => $validated['started_at'] ?? now(),
            'stopped_at' => $validated['stopped_at'] ?? now(),
        ]);

        return redirect()
            ->route('admin.time-entries.index', $ticket)
            ->with('status', 'Time entry added.');
    }

    public function destroy(TimeEntry $timeEntry): RedirectResponse
    {
        $this->authorize('manage', $timeEntry->ticket);

        $timeEntry->delete();

        return back()->with('status', 'Time entry deleted.');
    }
}
