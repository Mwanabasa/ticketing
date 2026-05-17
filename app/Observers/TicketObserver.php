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
        // Cast to JSON-safe scalars — getAttributes() can contain enum objects
        $old = collect($ticket->getOriginal())->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : $v)->toArray();
        $new = collect($ticket->getChanges())->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : $v)->toArray();
        $this->logAction('updated', $ticket, $old ?: null, $new ?: null);

        // Send notification if assigned to staff
        if ($ticket->wasChanged('assigned_to') && $ticket->assigned_to) {
            Mail::to($ticket->assignee)->send(new TicketAssignedNotification($ticket));
        }

        // Send notification if status changed
        if ($ticket->wasChanged('status')) {
            $oldStatus = $ticket->getOriginal('status');
            Mail::to($ticket->user)->send(new TicketStatusChangedNotification(
                $ticket,
                $oldStatus instanceof \App\Enums\TicketStatus ? $oldStatus->value : (string) $oldStatus,
                $ticket->status->value
            ));
        }
    }

    public function deleted(Ticket $ticket): void
    {
        $this->logAction('deleted', $ticket);
    }

    protected function logAction(string $action, Ticket $ticket, ?array $oldValues = null, ?array $newValues = null): void
    {
        AuditLog::query()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
