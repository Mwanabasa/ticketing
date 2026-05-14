<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'open' => Ticket::query()->where('status', TicketStatus::Open)->count(),
            'pending' => Ticket::query()->where('status', TicketStatus::Pending)->count(),
            'resolved' => Ticket::query()->where('status', TicketStatus::Resolved)->count(),
            'closed' => Ticket::query()->where('status', TicketStatus::Closed)->count(),
            'unassigned' => Ticket::query()->whereNull('assigned_to')->whereIn('status', [TicketStatus::Open, TicketStatus::Pending])->count(),
        ];

        $recentTickets = Ticket::query()
            ->with(['user', 'category', 'assignee'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTickets'));
    }
}
