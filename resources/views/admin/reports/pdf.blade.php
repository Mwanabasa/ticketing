<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

    .header { background: linear-gradient(135deg, #1e1b4b, #4338ca); color: white; padding: 24px 32px; margin-bottom: 24px; }
    .header h1 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
    .header p  { font-size: 11px; opacity: 0.75; }

    .section { margin: 0 32px 24px; }
    .section-title { font-size: 13px; font-weight: 700; color: #4338ca; border-bottom: 2px solid #e0e7ff; padding-bottom: 6px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }

    .stats-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
    .stat-box { display: table-cell; background: #f8faff; border: 1px solid #e0e7ff; border-radius: 8px; padding: 12px 16px; text-align: center; width: 20%; }
    .stat-box .num { font-size: 22px; font-weight: 800; color: #1e1b4b; }
    .stat-box .lbl { font-size: 9px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 2px; }

    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    thead tr { background: #1e1b4b; color: white; }
    thead th { padding: 8px 10px; text-align: left; font-weight: 600; font-size: 9px; text-transform: uppercase; letter-spacing: 0.06em; }
    tbody tr:nth-child(even) { background: #f8faff; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; }

    .badge { display: inline-block; border-radius: 99px; padding: 2px 8px; font-size: 9px; font-weight: 700; }
    .badge-open     { background: #dcfce7; color: #15803d; }
    .badge-pending  { background: #fef9c3; color: #a16207; }
    .badge-resolved { background: #dbeafe; color: #1d4ed8; }
    .badge-closed   { background: #f1f5f9; color: #475569; }

    .footer { margin: 32px 32px 0; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>

<div class="header">
    <h1>HelpDesk — Report</h1>
    <p>
        Generated {{ now()->format('F j, Y \a\t g:i A') }}
        @if ($from || $to)
            &nbsp;·&nbsp; Period:
            {{ $from ? $from->format('M j, Y') : 'beginning' }}
            –
            {{ $to ? $to->format('M j, Y') : 'today' }}
        @else
            &nbsp;·&nbsp; All time
        @endif
    </p>
</div>

{{-- Summary stats --}}
<div class="section">
    <div class="section-title">Summary</div>
    <div class="stats-grid">
        <div class="stat-box"><div class="num">{{ $totalTickets }}</div><div class="lbl">Total</div></div>
        <div class="stat-box"><div class="num">{{ $byStatus['open'] ?? 0 }}</div><div class="lbl">Open</div></div>
        <div class="stat-box"><div class="num">{{ $byStatus['pending'] ?? 0 }}</div><div class="lbl">Pending</div></div>
        <div class="stat-box"><div class="num">{{ $byStatus['resolved'] ?? 0 }}</div><div class="lbl">Resolved</div></div>
        <div class="stat-box"><div class="num">{{ $byStatus['closed'] ?? 0 }}</div><div class="lbl">Closed</div></div>
    </div>
</div>

{{-- Tickets by category --}}
@if ($byCategory->isNotEmpty())
<div class="section">
    <div class="section-title">Tickets by Category</div>
    <table>
        <thead><tr><th>Category</th><th>Count</th><th>% of Total</th></tr></thead>
        <tbody>
            @foreach ($byCategory as $name => $count)
            <tr>
                <td>{{ $name }}</td>
                <td>{{ $count }}</td>
                <td>{{ $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Staff performance --}}
@if ($staffPerformance->isNotEmpty())
<div class="section">
    <div class="section-title">Staff Performance</div>
    <table>
        <thead><tr><th>Name</th><th>Assigned</th><th>Resolved</th><th>Closed</th><th>Avg Rating</th></tr></thead>
        <tbody>
            @foreach ($staffPerformance as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->total_assigned }}</td>
                <td>{{ $member->resolved_count }}</td>
                <td>{{ $member->closed_count }}</td>
                <td>{{ $member->avg_rating ? number_format($member->avg_rating, 1) . ' / 5' : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Recent resolved --}}
@if ($recentResolved->isNotEmpty())
<div class="section">
    <div class="section-title">Recently Resolved Tickets</div>
    <table>
        <thead><tr><th>#</th><th>Subject</th><th>Student</th><th>Category</th><th>Resolved</th></tr></thead>
        <tbody>
            @foreach ($recentResolved as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ \Illuminate\Support\Str::limit($t->subject, 55) }}</td>
                <td>{{ $t->user->name }}</td>
                <td>{{ $t->category->name }}</td>
                <td>{{ $t->updated_at->format('M j, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">HelpDesk IT Support Portal · Confidential</div>

</body>
</html>
