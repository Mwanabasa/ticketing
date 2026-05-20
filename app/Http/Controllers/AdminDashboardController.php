<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Single query for status counts instead of 4 separate ones
        $counts = Ticket::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'open'           => $counts[TicketStatus::Open->value] ?? 0,
            'pending'        => $counts[TicketStatus::Pending->value] ?? 0,
            'resolved'       => $counts[TicketStatus::Resolved->value] ?? 0,
            'closed'         => $counts[TicketStatus::Closed->value] ?? 0,
            'unassigned'     => Ticket::query()->whereNull('assigned_to')->whereIn('status', [TicketStatus::Open, TicketStatus::Pending])->count(),
            'assigned_to_me' => Ticket::query()->where('assigned_to', $request->user()->id)->whereIn('status', [TicketStatus::Open, TicketStatus::Pending])->count(),
        ];

        $recentTickets = Ticket::query()
            ->with(['user', 'category', 'assignee'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTickets'));
    }
}
