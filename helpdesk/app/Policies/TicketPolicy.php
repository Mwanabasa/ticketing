<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isStaff() || $ticket->user_id === $user->id;
    }

    public function replyAsStudent(User $user, Ticket $ticket): bool
    {
        if (! $user->isStudent() || $ticket->user_id !== $user->id) {
            return false;
        }

        return $ticket->status !== TicketStatus::Closed;
    }

    public function manage(User $user, Ticket $ticket): bool
    {
        return $user->isStaff();
    }
}
