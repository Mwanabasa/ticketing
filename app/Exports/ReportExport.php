<?php

namespace App\Exports;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        private readonly ?Carbon $from,
        private readonly ?Carbon $to,
    ) {}

    public function sheets(): array
    {
        return [
            new TicketsSheet($this->from, $this->to),
            new StaffSheet(),
        ];
    }
}

class TicketsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly ?Carbon $from,
        private readonly ?Carbon $to,
    ) {}

    public function title(): string { return 'Tickets'; }

    public function headings(): array
    {
        return ['ID', 'Subject', 'Status', 'Priority', 'Category', 'Student', 'Assigned To', 'Created', 'Updated', 'Due Date', 'Rating'];
    }

    public function collection(): Collection
    {
        return Ticket::with(['user', 'category', 'assignee'])
            ->when($this->from, fn ($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to,   fn ($q) => $q->whereDate('created_at', '<=', $this->to))
            ->latest()
            ->get();
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->subject,
            $ticket->status->label(),
            $ticket->priority->label(),
            $ticket->category->name,
            $ticket->user->name,
            $ticket->assignee?->name ?? 'Unassigned',
            $ticket->created_at->format('Y-m-d H:i'),
            $ticket->updated_at->format('Y-m-d H:i'),
            $ticket->due_at?->format('Y-m-d H:i') ?? '',
            $ticket->rating ?? '',
        ];
    }
}

class StaffSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function title(): string { return 'Staff Performance'; }

    public function headings(): array
    {
        return ['Name', 'Email', 'Total Assigned', 'Resolved', 'Closed', 'Avg Rating'];
    }

    public function collection(): Collection
    {
        return User::where('role', UserRole::Staff)
            ->withCount([
                'assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => fn ($q) => $q->where('status', TicketStatus::Resolved),
                'assignedTickets as closed_count'   => fn ($q) => $q->where('status', TicketStatus::Closed),
            ])
            ->addSelect([
                'avg_rating' => Ticket::selectRaw('ROUND(AVG(rating), 2)')
                    ->whereColumn('assigned_to', 'users.id')
                    ->whereNotNull('rating'),
            ])
            ->orderByDesc('resolved_count')
            ->get();
    }

    public function map($member): array
    {
        return [
            $member->name,
            $member->email,
            $member->total_assigned,
            $member->resolved_count,
            $member->closed_count,
            $member->avg_rating ?? 'N/A',
        ];
    }
}
