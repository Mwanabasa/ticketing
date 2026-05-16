<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = $request->date('from');
        $to = $request->date('to');

        if ($from && $to && $to->lt($from)) {
            return redirect()
                ->route('admin.reports.index', ['from' => $from->format('Y-m-d')])
                ->withErrors(['to' => 'The end date must be on or after the start date.']);
        }

        $base = Ticket::query()
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to));

        $byStatus = (clone $base)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $byCategory = (clone $base)
            ->join('categories', 'categories.id', '=', 'tickets.category_id')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->pluck('total', 'name');

        $totalTickets = (clone $base)->count();

        $recentResolved = Ticket::query()
            ->with(['user', 'category'])
            ->where('status', TicketStatus::Resolved)
            ->when($from, fn ($q) => $q->whereDate('updated_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('updated_at', '<=', $to))
            ->latest('updated_at')
            ->limit(20)
            ->get();

        return view('admin.reports.index', [
            'byStatus' => $byStatus,
            'byCategory' => $byCategory,
            'totalTickets' => $totalTickets,
            'recentResolved' => $recentResolved,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
