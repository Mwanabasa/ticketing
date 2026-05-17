<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'description',
        'duration_minutes',
        'started_at',
        'stopped_at',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'started_at' => 'datetime',
            'stopped_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_minutes / 60, 2);
    }
}
