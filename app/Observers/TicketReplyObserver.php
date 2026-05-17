<?php

namespace App\Observers;

use App\Mail\TicketReplyNotification;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Mail;

class TicketReplyObserver
{
    public function created(TicketReply $reply): void
    {
        $ticket = $reply->ticket;
        
        // Notify the ticket owner if the reply is from staff
        if ($reply->user->isStaff() && $reply->user_id !== $ticket->user_id) {
            Mail::to($ticket->user)->send(new TicketReplyNotification($ticket, $reply));
        }
        
        // Notify the assigned staff if the reply is from the student
        if ($reply->user->isStudent() && $ticket->assigned_to) {
            Mail::to($ticket->assignee)->send(new TicketReplyNotification($ticket, $reply));
        }
    }
}
