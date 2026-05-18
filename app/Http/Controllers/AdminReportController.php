<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Exports\ReportExport;
use App\Models\Ticket;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminReportController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
        ]);

        $from = $request->date('from');
        $to   = $request->date('to');

        if ($from && $to && $to->lt($from)) {
            return redirect()
                ->route('admin.reports.index', ['from' => $from->format('Y-m-d')])
                ->withErrors(['to' => 'The end date must be on or after the start date.']);
        }

        $base = Ticket::query()
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('created_at', '<=', $to));

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
            ->when($to,   fn ($q) => $q->whereDate('updated_at', '<=', $to))
            ->latest('updated_at')
            ->limit(20)
            ->get();

        $staffPerformance = User::query()
            ->where('role', UserRole::Staff)
            ->withCount([
                'assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => fn ($q) => $q->where('status', TicketStatus::Resolved),
                'assignedTickets as closed_count'   => fn ($q) => $q->where('status', TicketStatus::Closed),
            ])
            ->addSelect([
                'avg_rating' => Ticket::query()
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('assigned_to', 'users.id')
                    ->whereNotNull('rating'),
            ])
            ->orderByDesc('resolved_count')
            ->get();

        return view('admin.reports.index', [
            'byStatus'         => $byStatus,
            'byCategory'       => $byCategory,
            'totalTickets'     => $totalTickets,
            'recentResolved'   => $recentResolved,
            'staffPerformance' => $staffPerformance,
            'from'             => $from,
            'to'               => $to,
        ]);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);

        $from     = $request->date('from');
        $to       = $request->date('to');
        $filename = 'helpdesk-report-' . now()->format('Y-m-d') . '.xlsx';

        return (new ReportExport($from, $to))->download($filename);
    }

    public function exportPdf(Request $request): Response
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);

        $from = $request->date('from');
        $to   = $request->date('to');

        $base = Ticket::query()
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('created_at', '<=', $to));

        $byStatus   = (clone $base)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $byCategory = (clone $base)->join('categories', 'categories.id', '=', 'tickets.category_id')->select('categories.name', DB::raw('count(*) as total'))->groupBy('categories.name')->orderByDesc('total')->pluck('total', 'name');
        $totalTickets = (clone $base)->count();

        $recentResolved = Ticket::with(['user', 'category'])
            ->where('status', TicketStatus::Resolved)
            ->when($from, fn ($q) => $q->whereDate('updated_at', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('updated_at', '<=', $to))
            ->latest('updated_at')->limit(20)->get();

        $staffPerformance = User::where('role', UserRole::Staff)
            ->withCount([
                'assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => fn ($q) => $q->where('status', TicketStatus::Resolved),
                'assignedTickets as closed_count'   => fn ($q) => $q->where('status', TicketStatus::Closed),
            ])
            ->addSelect(['avg_rating' => Ticket::selectRaw('AVG(rating)')->whereColumn('assigned_to', 'users.id')->whereNotNull('rating')])
            ->orderByDesc('resolved_count')->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact(
            'byStatus', 'byCategory', 'totalTickets', 'recentResolved', 'staffPerformance', 'from', 'to'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('helpdesk-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
