<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1e293b; background: #f8fafc; padding: 32px;">
    <div style="max-width: 560px; margin: 0 auto; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px;">
        <p style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6366f1; margin: 0 0 8px;">HelpDesk Notification</p>
        <h1 style="font-size: 20px; font-weight: 700; margin: 0 0 16px;">New reply on ticket #{{ $ticket->id }}</h1>
        <p style="color: #475569; margin: 0 0 24px;">
            <strong>{{ $reply->user->name }}</strong> replied to your ticket.
        </p>

        <div style="background: #f1f5f9; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <p style="margin: 0 0 4px; font-size: 13px; font-weight: 600; color: #64748b;">{{ $ticket->subject }}</p>
        </div>

        <div style="border-left: 3px solid #6366f1; padding-left: 16px; margin-bottom: 24px; color: #334155;">
            <p style="margin: 0; white-space: pre-wrap; font-size: 14px;">{{ $reply->body }}</p>
        </div>

        <a href="{{ $ticket->user->isStaff() ? route('admin.tickets.show', $ticket) : route('student.tickets.show', $ticket) }}"
           style="display: inline-block; background: #4f46e5; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px;">
            View ticket
        </a>

        <p style="margin: 24px 0 0; font-size: 12px; color: #94a3b8;">This is an automated notification from HelpDesk.</p>
    </div>
</body>
</html>
