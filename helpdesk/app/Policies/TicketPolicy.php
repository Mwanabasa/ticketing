<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStaff()) {
            return $ticket->assigned_to === $user->id;
        }

        return $ticket->user_id === $user->id;
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
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStaff() && $ticket->assigned_to === $user->id;
    }
}
