<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'assigned_to',
        'subject',
        'description',
        'status',
        'priority',
        'attachment_path',
        'due_at',
        'sla_breached_at',
        'rating',
        'rating_comment',
    ];

    protected function casts(): array
    {
        return [
            'status'          => TicketStatus::class,
            'priority'        => TicketPriority::class,
            'due_at'          => 'datetime',
            'sla_breached_at' => 'datetime',
            'rating'          => 'integer',
        ];
    }

    public function isOverdue(): bool
    {
        return $this->due_at !== null
            && $this->due_at->isPast()
            && ! in_array($this->status, [TicketStatus::Resolved, TicketStatus::Closed]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function totalTimeSpent(): int
    {
        return $this->timeEntries()->sum('duration_minutes');
    }
}
