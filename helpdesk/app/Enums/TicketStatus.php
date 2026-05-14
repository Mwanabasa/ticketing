<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Pending = 'pending';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Pending => 'Pending',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Open => 'bg-emerald-100 text-emerald-800',
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Resolved => 'bg-blue-100 text-blue-800',
            self::Closed => 'bg-zinc-200 text-zinc-700',
        };
    }
}
