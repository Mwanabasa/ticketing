<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $baseQuery = Ticket::query()->where('user_id', $user->id);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->where('status', TicketStatus::Open)->count(),
            'pending' => (clone $baseQuery)->where('status', TicketStatus::Pending)->count(),
            'resolved' => (clone $baseQuery)->where('status', TicketStatus::Resolved)->count(),
            'closed' => (clone $baseQuery)->where('status', TicketStatus::Closed)->count(),
        ];

        $recentTickets = Ticket::query()
            ->where('user_id', $user->id)
            ->with(['category', 'assignee'])
            ->latest('updated_at')
            ->limit(8)
            ->get();

        return view('student.dashboard', [
            'activeCount' => $stats['open'] + $stats['pending'],
            'recentTickets' => $recentTickets,
            'stats' => $stats,
        ]);
    }
}
