<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

class NotificationService
{
    public static function notifyReply(Ticket $ticket, TicketReply $reply, User $recipient): void
    {
        Notification::create([
            'user_id' => $recipient->id,
            'type'    => 'reply',
            'title'   => 'New reply on ticket #'.$ticket->id,
            'body'    => $reply->user->name.': '.str($reply->body)->limit(100),
            'url'     => $recipient->isStaff()
                ? route('admin.tickets.show', $ticket)
                : route('student.tickets.show', $ticket),
        ]);
    }

    public static function notifyStatusChanged(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        Notification::create([
            'user_id' => $ticket->user_id,
            'type'    => 'status_changed',
            'title'   => 'Ticket #'.$ticket->id.' status updated',
            'body'    => 'Status changed from '.$oldStatus.' to '.$newStatus.'.',
            'url'     => route('student.tickets.show', $ticket),
        ]);
    }

    public static function notifyAssigned(Ticket $ticket, User $assignee): void
    {
        Notification::create([
            'user_id' => $assignee->id,
            'type'    => 'assigned',
            'title'   => 'Ticket #'.$ticket->id.' assigned to you',
            'body'    => $ticket->subject,
            'url'     => route('admin.tickets.show', $ticket),
        ]);
    }

    public static function notifyRatingReceived(Ticket $ticket): void
    {
        if (! $ticket->assigned_to) return;

        Notification::create([
            'user_id' => $ticket->assigned_to,
            'type'    => 'rating',
            'title'   => 'Ticket #'.$ticket->id.' was rated',
            'body'    => 'Rating: '.str_repeat('★', $ticket->rating).str_repeat('☆', 5 - $ticket->rating),
            'url'     => route('admin.tickets.show', $ticket),
        ]);
    }
}
