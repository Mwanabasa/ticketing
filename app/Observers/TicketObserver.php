<?php

namespace App\Observers;

use App\Mail\TicketAssignedNotification;
use App\Mail\TicketStatusChangedNotification;
use App\Models\AuditLog;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        $this->logAction('created', $ticket);
    }

    public function updated(Ticket $ticket): void
    {
        $old = collect($ticket->getOriginal())->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : $v)->toArray();
        $new = collect($ticket->getChanges())->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : $v)->toArray();

        $this->logAction('updated', $ticket, $old ?: null, $new ?: null);

        // Track SLA breach
        if ($ticket->isOverdue() && ! $ticket->sla_breached_at) {
            $ticket->timestamps = false;
            $ticket->updateQuietly(['sla_breached_at' => now()]);
            $ticket->timestamps = true;
        }

        if ($ticket->wasChanged('assigned_to') && $ticket->assigned_to && $ticket->assignee) {
            Mail::to($ticket->assignee)->queue(new TicketAssignedNotification($ticket));
        }

        if ($ticket->wasChanged('status')) {
            $oldStatus = $ticket->getOriginal('status');
            $oldVal    = $oldStatus instanceof \App\Enums\TicketStatus ? $oldStatus->value : (string) $oldStatus;
            Mail::to($ticket->user)->queue(new TicketStatusChangedNotification($ticket, $oldVal, $ticket->status->value));
        }
    }

    public function deleted(Ticket $ticket): void
    {
        $this->logAction('deleted', $ticket);
    }

    protected function logAction(string $action, Ticket $ticket, ?array $oldValues = null, ?array $newValues = null): void
    {
        AuditLog::query()->create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => Ticket::class,
            'model_id'   => $ticket->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
