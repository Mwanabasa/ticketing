<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'open' => Ticket::query()->where('status', TicketStatus::Open)->count(),
            'pending' => Ticket::query()->where('status', TicketStatus::Pending)->count(),
            'resolved' => Ticket::query()->where('status', TicketStatus::Resolved)->count(),
            'closed' => Ticket::query()->where('status', TicketStatus::Closed)->count(),
            'assigned_to_me' => Ticket::query()
                ->where('assigned_to', $request->user()->id)
                ->whereIn('status', [TicketStatus::Open, TicketStatus::Pending])
                ->count(),
        ];

        $assignedTickets = Ticket::query()
            ->with(['user', 'category'])
            ->where('assigned_to', $request->user()->id)
            ->whereIn('status', [TicketStatus::Open, TicketStatus::Pending])
            ->latest()
            ->limit(8)
            ->get();

        return view('staff.dashboard', compact('stats', 'assignedTickets'));
    }
}
