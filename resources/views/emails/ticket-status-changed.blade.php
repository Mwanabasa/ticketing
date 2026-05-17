<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1e293b; background: #f8fafc; padding: 32px;">
    <div style="max-width: 560px; margin: 0 auto; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px;">
        <p style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6366f1; margin: 0 0 8px;">HelpDesk Notification</p>
        <h1 style="font-size: 20px; font-weight: 700; margin: 0 0 16px;">Ticket #{{ $ticket->id }} status updated</h1>
        <p style="color: #475569; margin: 0 0 24px;">The status of your support ticket has changed.</p>

        <div style="background: #f1f5f9; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <p style="margin: 0 0 8px; font-weight: 600;">{{ $ticket->subject }}</p>
            <p style="margin: 0; font-size: 13px; color: #64748b;">
                <span style="text-decoration: line-through;">{{ \App\Enums\TicketStatus::tryFrom($oldStatus)?->label() ?? $oldStatus }}</span>
                &nbsp;→&nbsp;
                <strong style="color: #1e293b;">{{ \App\Enums\TicketStatus::tryFrom($newStatus)?->label() ?? $newStatus }}</strong>
            </p>
        </div>

        @if ($ticket->status === \App\Enums\TicketStatus::Resolved)
            <p style="color: #475569; margin: 0 0 24px; font-size: 14px;">
                Your issue has been marked as resolved. If you still need help, you can reply to reopen it.
            </p>
        @elseif ($ticket->status === \App\Enums\TicketStatus::Closed)
            <p style="color: #475569; margin: 0 0 24px; font-size: 14px;">
                This ticket has been closed. Please open a new ticket if you need further assistance.
            </p>
        @endif

        @if ($ticket->status !== \App\Enums\TicketStatus::Closed)
            <a href="{{ route('student.tickets.show', $ticket) }}"
               style="display: inline-block; background: #4f46e5; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                View ticket
            </a>
        @endif

        <p style="margin: 24px 0 0; font-size: 12px; color: #94a3b8;">This is an automated notification from HelpDesk.</p>
    </div>
</body>
</html>
