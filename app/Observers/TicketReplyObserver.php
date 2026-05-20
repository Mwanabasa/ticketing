<?php

namespace App\Observers;

use App\Mail\TicketReplyNotification;
use App\Models\AuditLog;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class TicketReplyObserver
{
    public function created(TicketReply $reply): void
    {
        $ticket = $reply->ticket;

        if ($reply->user->isStaff() && $reply->user_id !== $ticket->user_id) {
            try {
                Mail::to($ticket->user)->send(new TicketReplyNotification($ticket, $reply));
            } catch (\Throwable) {}
        }

        if ($reply->user->isStudent() && $ticket->assigned_to) {
            try {
                Mail::to($ticket->assignee)->send(new TicketReplyNotification($ticket, $reply));
            } catch (\Throwable) {}
        }

        AuditLog::query()->create([
            'user_id'    => Auth::id(),
            'action'     => 'replied',
            'model_type' => \App\Models\Ticket::class,
            'model_id'   => $ticket->id,
            'old_values' => null,
            'new_values' => ['reply_id' => $reply->id, 'by' => $reply->user->name],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
